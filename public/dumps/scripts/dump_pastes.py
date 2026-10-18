#!/usr/bin/env python3
# Script to dump pastes from the ponepaste database daily (or less frequently).
# Dumps are stored in the dump_dir directory and old dumps are cleaned up according to the retention policy.
# Retention policy keeps the earliest dump, the most recent dump, the earliest dump of each month and all delta files.

# Usage: dump_pastes.py <path to dump target directory> <path to dump_pastes.php>
# Example: dump_pastes.py /path/to/dump/dir /path/to/dump_pastes.php
# This script should be run daily to maintain an up-to-date archive of pastes, but
# it handles if it isn't executed daily (generates the delta between the previous and the current dump).
# Required environment variables (exported):
# - PP_ENCRYPTION_KEY - encryption key for the ponepaste database
# - PP_USER - database user for the ponepaste database
# - PP_PASS - password for the ponepaste database user
# - PP_DATABASE_SERVER - server address (ip or hostname) of the ponepaste database
# - PP_DATABASE - name of the ponepaste database

import argparse
import datetime
import os
import re
import shutil
import subprocess
import sys
import tempfile
from pathlib import Path

DATE_ARCHIVE = re.compile(r"\d{4}-\d{2}-\d{2}\.tar\.gz\Z")


def dated_archives(dump_dir: Path):
    return sorted(
        path for path in dump_dir.iterdir()
        if DATE_ARCHIVE.fullmatch(path.name) and path.is_file()
    )


def publish_copy(source: Path, destination: Path):
    temporary = destination.parent / f".{destination.name}.{os.getpid()}.tmp"
    try:
        print(f"Publishing copy from {source.absolute() } to {destination.absolute() }")
        shutil.copy2(source, temporary)
        os.replace(temporary, destination)
    finally:
        temporary.unlink(missing_ok=True)

def clean_old_archives(dump_dir: Path):
    archives = dated_archives(dump_dir)
    if not archives:
        return

    keep = {archives[0], archives[-1]} # keep the earliest and the most recent archives
    earliest_by_month = {}
    for archive in archives:
        month = archive.name[:7] # extract the year and month portion of the archive name
        earliest_by_month.setdefault(month, archive) # keep the earliest archive for each month
    keep.update(earliest_by_month.values())

    for archive in archives:
        if archive in keep:
            print(f"Keeping archive {archive.absolute() }")
            continue
        try:
            print(f"Deleting old archive {archive.absolute() }")
            archive.unlink()
        except OSError as error:
            print(f"Warning: could not delete {archive.name}: {error}", file=sys.stderr)


def main(dump_dir: Path, dump_script: Path):
    # Do not change it from isoformat to another! Else sorting by name breaks. 
    today = datetime.date.today().isoformat()
    today_archive = dump_dir / f"{today}.tar.gz"

    if today_archive.exists():
        print(f"Warning: today's dump already exists: {today_archive.absolute()}", file=sys.stderr)
        return 0
    if not dump_dir.is_dir():
        print(f"Dump directory does not exist: {dump_dir}", file=sys.stderr)
        return 1
    if not dump_script.is_file():
        print(f"PHP dump script not found: {dump_script}", file=sys.stderr)
        return 1

    try:
        with tempfile.TemporaryDirectory(prefix="ponepaste-dump-") as temporary:
            workdir = Path(temporary)
            php_dump_dir = workdir / today
            archive = workdir / f"{today}.tar.gz"
            print(f"Temporary directory: {workdir}")

            print('Running "dump_pastes.php"')
            subprocess.run(
                ["php", "-f", str(dump_script), f"{php_dump_dir}{os.sep}"],
                check=True,
            )
            print('Finished running "dump_pastes.php"')

            print("Compressing the dump")
            subprocess.run(
                ["tar", "-czf", str(archive), today],
                cwd=workdir,
                check=True,
            )
            print(f"Compressed dump to {archive.name}")

            existing = dated_archives(dump_dir)
            if not existing:
                publish_copy(archive, today_archive)
                try:
                    publish_copy(today_archive, dump_dir / "latest.tar.gz")
                    print("Finished updating latest.tar.gz")
                except OSError as error:
                    print(f"Warning: could not update latest.tar.gz: {error}", file=sys.stderr)
                print("No existing dumps found, created initial dump")
                return 0

            previous = existing[-1]
            previous_date = previous.name.removesuffix(".tar.gz")
            delta_name = f"{previous_date}_{today}.xdelta3"
            delta = workdir / delta_name
            print(f"Creating xdelta3 between {previous.name} and {today_archive.name}")
            subprocess.run(
                ["xdelta3", "-f", "-e", "-s", str(previous), str(archive), str(delta)],
                check=True,
            )
            print("Finished creating xdelta3")

            print(f"Copying the dump and xdelta3 file to the dump_dir as {today_archive.name} and {delta_name}")
            publish_copy(archive, today_archive)
            publish_copy(delta, dump_dir / delta_name)

            print(f"Updating latest.tar.gz to point to the new {today_archive.name}")
            try:
                publish_copy(today_archive, dump_dir / "latest.tar.gz")
            except OSError as error:
                print(f"Warning: could not update latest.tar.gz: {error}", file=sys.stderr)
            else:
                print("Finished updating latest.tar.gz")

            print("Cleaning up old dumps according to the retention policy")
            clean_old_archives(dump_dir)
        return 0
    except (OSError, subprocess.CalledProcessError) as error:
        print(f"Dump failed: {error}", file=sys.stderr)
        return 1
    finally:
        print("Cleaning up the temporary directory")


if __name__ == "__main__":
    # verify that the required environment variables are set
        
    if not all([os.getenv("PP_ENCRYPTION_KEY"),
                os.getenv("PP_USER"), 
                os.getenv("PP_PASS"),
                os.getenv("PP_DATABASE_SERVER"),
                os.getenv("PP_DATABASE")]):
        print("Error: One or more required environment variables are not set.", file=sys.stderr)
        sys.exit(1)

    # get the paths for the public dumps directory and the PHP dump script from the command line arguments
    parser = argparse.ArgumentParser()
    parser.add_argument("dump_dir", type=Path, help="Path to the public dumps directory")
    parser.add_argument("dump_script", type=Path, help="Path to the PHP dump script")
    args = parser.parse_args()
    dump_dir = args.dump_dir
    dump_script = args.dump_script

    # remove the trailing slash from the dump_dir if it exists
    dump_dir = dump_dir.resolve()
    
    sys.exit(main(dump_dir, dump_script))
