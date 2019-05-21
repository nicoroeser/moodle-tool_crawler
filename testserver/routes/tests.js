var express = require('express');
var router = express.Router();

var longhdr = '';
for (i = 0; i < 100; ++i) {
    longhdr += 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
}

/* Sends a *large* header (around 2 MiB) with not very many header fields (500).
 * So each header field is about 4 kB in size.
 *
 * A Stack Overflow answer on HTTP header size
 * <https://stackoverflow.com/a/686243> by user vartec
 * <https://stackoverflow.com/users/60711/vartec> and edited by the Stack
 * Overflow community may be interesting.
 */
function bigheader(req, res) {
    smallheader(req, res);
    for (i = 0; i < 500; i++) {
        res.set('X-Dummy-' + ('000' + i).slice(-4), longhdr);
    }
}

/* Sends a *small* header (a few bytes).
 */
function smallheader(req, res) {
    if (req.query.type === 'plain') {
        var type = 'text/plain';
    } else {
        // We do not really deliver a HTML document, but we pretend to.
        var type = 'text/html';
    }
    res.set('Content-Type', type + '; charset=UTF-8');
}

/* Adds a redirection (always uses 307 Temporary Redirect, which does not allow
 * the client to change the request method).
 *
 * @param dst the target URI.
 */
function redirect_to(req, res, dst) {
    var plain = '';
    if (req.query.type === 'plain') {
        plain = '?type=plain';
    }
    res.set('Location', dst + plain);
    res.status(307, 'Temporary Redirect');
}

/* Sends a *large* body (around 16 MiB) and ends the response.
 */
function bigbody(res) {
    for (i = 0; i < 16000; i++) {
        res.write('This document is a repetition of a dummy text with around 1000 bytes.\n' +
                'Actually, it is a CC0-licensed image, encoded with Base64 to produce a series of printable characters. We hope you like it. 😀\n' +
                'Here comes the image: /9j/4AAQSkZJRgABAQEASABIAAD/2wBDACAWGBwYFCAcGhwkIiAmMFA0MCwsMGJGSjpQdGZ6eHJm\n' +
                'cG6AkLicgIiuim5woNqirr7EztDOfJri8uDI8LjKzsb/2wBDASIkJDAqMF40NF7GhHCExsbGxsbG\n' +
                'xsbGxsbGxsbGxsbGxsbGxsbGxsbGxsbGxsbGxsbGxsbGxsbGxsbGxsbGxsb/wAARCAAoADwDASEA\n' +
                'AhEBAxEB/8QAGQAAAwEBAQAAAAAAAAAAAAAAAQIDBAAF/8QAKBAAAQMDAgUFAQEAAAAAAAAAAQAC\n' +
                'EQMSIQQxIjJBUYETYXGRobHw/8QAFgEBAQEAAAAAAAAAAAAAAAAAAAEC/8QAGREBAQEBAQEAAAAA\n' +
                'AAAAAAAAABEBQSEC/9oADAMBAAIRAxEAPwCHog4JnwqMogbY8IwcUoOXFUFMN6/qVU5JPQrocd7Y\n' +
                '+EqAYg4EqZ36K1RDak8qeH9lkmhUe6nYYm4wou1PGccPdI1DBxqkNHLuXKkBowSAOypAgHuUpgHI\n' +
                'KC98rrx/ikKDiHBoIBJOPZQqUSTFrbIxG6nV46gwNBGQr2Db+lNZ0CCB08IcI3dlMCstjinwnbRY\n' +
                'ZIf9ou/JN6pIPC0QFqbpvWbjBA3SVbE36OqyLW3Z7xj5Wo6XTt3/AF6151A9PTNGA2PmUjnaYHZn\n' +
                '0lwjzKhIMNmUaTXTzEhZVopsgdkRq6gIY1gtnmJMpo1VgG0yQ6T0WJxquPM0JmJpXNduahHsFMtB\n' +
                'OXOV8H//2Q==\n');
    }
    res.end();
}

/* Sends a *small* body (a few bytes) and ends the response.
 */
function smallbody(res) {
    res.write('This is the resource.\n');
    res.end();
}


/* *** Functions which handle the test cases. *** */

router.get('/test1', function(req, res) {
    smallheader(req, res);
    // no redirection
    smallbody(res);
});

router.get('/test2', function(req, res) {
    smallheader(req, res);
    // no redirection
    bigbody(res);
});

router.get('/test3', function(req, res) {
    bigheader(req, res);
    // no redirection
    smallbody(res);
});

router.get('/test4', function(req, res) {
    bigheader(req, res);
    // no redirection
    bigbody(res);
});


router.get('/test5', function(req, res) {
    smallheader(req, res);
    redirect_to(req, res, '/tests/test5a');
    smallbody(res);
});

router.get('/test5a', function(req, res) {
    smallheader(req, res);
    redirect_to(req, res, '/tests/test5b');
    smallbody(res);
});

router.get('/test5b', function(req, res) {
    smallheader(req, res);
    // no further redirection
    smallbody(res);
});


router.get('/test6', function(req, res) {
    smallheader(req, res);
    redirect_to(req, res, '/tests/test6a');
    smallbody(res);
});

router.get('/test6a', function(req, res) {
    smallheader(req, res);
    redirect_to(req, res, '/tests/test6b');
    smallbody(res);
});

router.get('/test6b', function(req, res) {
    smallheader(req, res);
    // no further redirection
    bigbody(res);
});


router.get('/test7', function(req, res) {
    smallheader(req, res);
    redirect_to(req, res, '/tests/test7a');
    smallbody(res);
});

router.get('/test7a', function(req, res) {
    bigheader(req, res);
    redirect_to(req, res, '/tests/test7b');
    smallbody(res);
});

router.get('/test7b', function(req, res) {
    smallheader(req, res);
    // no further redirection
    bigbody(res);
});


router.get('/test8', function(req, res) {
    smallheader(req, res);
    redirect_to(req, res, '/tests/test8a');
    smallbody(res);
});

router.get('/test8a', function(req, res) {
    smallheader(req, res);
    redirect_to(req, res, '/tests/test8b');
    bigbody(res);
});

router.get('/test8b', function(req, res) {
    smallheader(req, res);
    // no further redirection
    smallbody(res);
});


module.exports = router;
