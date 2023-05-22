<main class="bd-main">
    <div class="bd-main-container container">
        <div class="bd-lead">
            <div class="rule">
                <h2>The short version</h2>
                <p>
                    We collect only the <em>bare minimum</em> amount of information that is necessary to protect
                    the service against abuse. We <em>do not sell</em> your information to third parties, and we
                    only use it as this document describes. We aim to be compliant with the <a
                        href="https://gdpr-info.eu/" rel="external noopener" target="_blank">EU <abbr
                            title="General Data Protection Regulation">GDPR</abbr></a>.
                </p>
            </div>
            <div class="rule">
                <h2>What information we collect and why</h2>
                <h3>Information from server logs</h3>
                <p>We collect the following information (in web server logs) from every visitor:</p>
                <ul>
                    <li>The visitor's Internet Protocol (IP) address</li>
                    <li>The date and time of the request</li>
                    <li>The page that was requested</li>
                    <li>The user agent string of the visitor's browser</li>
                </ul>
                <p>These items are collected to ensure the security of the service, and are deleted after 14
                    days to balance our "legitimate interest" (as mentioned in the GDPR) of security with user
                    privacy.</p>
                <h3>Information in cookies</h3>
                <p>Our cookies for any users of the service may contain:</p>
                <ul>
                    <li>A unique PHP session token</li>
                    <li>One or more "flash" messages (temporary notifications of an action's success or failure,
                        to be displayed at the top of the next page load and then deleted)
                    </li>
                </ul>
                <p>Additionally, cookies of users that are logged into the service may contain:</p>
                <ul>
                    <li>A random authentication secret ("remember me" token) unique to the user to persist their login</li>
                </ul>
                <p>These data are required for authentication, user security, or customization, which are all
                    "legitimate interests" as above, and thus we cannot ask for consent to use these
                    cookies.</p>
                <h3>Information in user-submitted content</h3>
                <p>
                    User-submitted content is considered to collectively refer to any content that you may
                    submit to the site, which includes, but is not limited to: comments, images, messsages,
                    posts, reports, source changes, tag changes, and votes.
                </p>
                <p>User-submitted content by users (authenticated or not) may contain any or all the following
                    information:</p>
                <ul>
                    <li>The IP address at the time of submission</li>
                    <li>The browser user agent string</li>
                    <li>The page that initiated the submission</li>
                </ul>
                <p>These items are only used for the "legitimate interests" of identifying and controlling abuse
                    of the service and are not shared with any external party.</p>
            </div>
            <div class="rule">
                <h2>Information from users with accounts</h2>
                <p>If you <strong>create an account</strong> we require some basic information at the time of
                    account creation. You will be asked to provide:</p>
                <ul>
                    <li>a username, shown on your profile and generally only visible to you and site staff</li>
                    <li>a password, stored only as a cryptographic hash</li>
                </ul>
                <p>We also store your IP address whenever you log in for security reasons.</p>
            </div>
            <div class="rule">
                <h2>Information that we do not collect</h2>
                <p>
                    <em>We do not intentionally collect personal information</em>, but users may include it in
                    user-submitted content. We will remove personal information if we deem it too sensitive.
                    Inform us if you believe shared information is too sensitive.
                </p>
                <p>This is especially important because information shared in public user-submitted content may
                    be indexed by search engines or used by third parties without your consent.</p>
            </div>
            <div class="rule">
                <h2>Information that may potentially be shared with third parties</h2>
                <p>
                    We do not in any way share individual account information with third parties unless legally
                    compelled to do so.
                </p>
                <p>Most of the site is public-facing, and third parties may access and use it.</p>
            </div>
            <div class="rule">
                <h2>How we secure your information</h2>
                <p>We take <em>all measures reasonably necessary</em> to protect account information from
                    unauthorized access, alteration, or destruction.</p>
                <p>
                    While in transit, your data are <em>always</em> protected by the latest version of <a
                        href="https://en.wikipedia.org/wiki/Transport_Layer_Security" rel="external noopener"
                        target="_blank">Transport Layer Security (TLS)</a> our software supports.
                    To protect user data on our servers, we strictly limit access, and require the use of
                    elliptic <a href="https://en.wikipedia.org/wiki/Curve25519" rel="external noopener"
                                target="_blank">Ed25519</a> or 4096-bit <a
                        href="https://en.wikipedia.org/wiki/RSA_(cryptosystem)" rel="external noopener"
                        target="_blank">RSA</a> keys for server login.
                </p>
                <p>
                    HTTPS is required for <em>all connections</em> to our service. Our cookies use a "<a
                        href="https://en.wikipedia.org/wiki/Secure_cookie" rel="external noopener"
                        target="_blank">secure</a>" setting and may only be transmitted privately to us. We use
                    a restrictive <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP"
                                     rel="external noopener" target="_blank">Content Security Policy (CSP)</a>
                    to protect against page hijacking and information leakage to third parties, an image proxy
                    server to avoid leaking user IP address information from embedded images on the site, a <a
                        href="https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS" rel="external noopener"
                        target="_blank">Cross-Origin Resource Sharing (CORS)</a> policy to restrict third-party
                    usage, a strict <a
                        href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy"
                        rel="external noopener" target="_blank">Referrer-Policy</a> to prevent leaking data for
                    external links, and a frame policy to prevent clickjacking.
                </p>
                <p>Passwords are hashed using <a href="https://en.wikipedia.org/wiki/Bcrypt"
                                                 rel="external noopener" target="_blank">bcrypt</a> at
                    2<sup>10</sup> iterations with a 128-bit per-user salt.</p>
                <p>No method of transmission, or method of electronic storage, is 100% secure. Therefore, we
                    cannot guarantee its absolute security; we only make our best effort.</p>
            </div>
            <div class="rule">
                <h2>Resolving complaints</h2>
                <p>
                    If you have concerns about the way we are handling your personal information, please let us
                    know immediately. You may contact us via email directly at <a
                        href="mailto:admin@ponepaste.org">admin@ponepaste.org</a>.
                </p>
            </div>
        </div>
    </div>
</main>