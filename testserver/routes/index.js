var express = require('express');
var router = express.Router();

/* GET home page. */
router.get('/', function(req, res) {
    console.log("method: " + req.method);
    res.set('Allow', 'GET, POST');
    res.status(405, 'Method Not Allowed (you stupid client)');
    res.send('nope');
    res.end();
  //res.render('index', { title: 'Express' });
});

router.get('/len', function(req, res) {
    //res.set('Content-Length', '22');
    res.set('Content-Type', 'text/html');
    res.status(200, 'OK (Yeah)');
    res.write('<!DOCTYPE html>\n<html><head><title>This document is longer than 22 octets.</title></head><body>foo</body><html>\n');
    res.write('\n');
    res.write('\n');
    res.end();
});

module.exports = router;
