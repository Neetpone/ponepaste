# Archive-Friendly Public Paste Dumps

These scripts export publicly available pastes in a format suitable for archiving.

## Prerequisites

### Applications

- PHP CLI with the ponepaste PHP dependencies
- Python 3
- `tar`
- `xdelta3` (required for daily incremental dumps)

### Environment Variables

Before running either script, export the database settings:

```sh
export PP_ENCRYPTION_KEY='<ponepaste encryption key>'
export PP_USER='<database user>'
export PP_PASS='<database password>'
export PP_DATABASE_SERVER='<database host>'
export PP_DATABASE='<database name>'
```

## One-Time Dump

Run from this folder, or provide the path to the PHP script. The output directory must not already exist; its parent directory must exist.

```sh
php -f ./dump_pastes.php /path/to/new/output-directory
```

## Daily Dumps

The destination directory must already exist. Run the Python script with that directory and the path to the PHP dumper:

```sh
python3 ./dump_pastes.py /path/to/dump-directory ./dump_pastes.php
```

Each run creates a `yyyy-MM-dd.tar.gz` dump; if that date's archive already exists, the script exits without creating another. After the initial dump, each run also creates an `.xdelta3` incremental file from the newest dated dump to the new one. `latest.tar.gz` is refreshed to contain the newest dump.

Old dated dumps are removed according to the retention policy: the earliest dump, the newest dump, and the earliest dump of each month are kept. Incremental `.xdelta3` files are not removed by this clean-up.

Recommended to run it daily, but handles if it is ran less frequently.

## Archive format

Once extracted from the .tar.gz, you see pastes.csv file. See it's header row for the column names/order. The `format` column contains the desired rendering format of the paste (plaintext/green/pastedown). The `tags` contain comma-separated tags associated with the paste. The rest is self explanatory.

The remaining files in the `data` directory correspond to the individual pastes, named by their paste ID.