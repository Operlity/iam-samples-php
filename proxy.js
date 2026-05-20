/**
 * Simple HTTPS to HTTP Proxy
 * Bridges https://localhost:4500 -> http://localhost:8000
 */
const https = require('https');
const http = require('http');
const fs = require('fs');
const path = require('path');

const TARGET_PORT = 8000;
const PROXY_PORT = 4500;

const sslOptions = {
    key: fs.readFileSync(path.join(__dirname, 'certificates', 'key.pem')),
    cert: fs.readFileSync(path.join(__dirname, 'certificates', 'cert.pem'))
};

const server = https.createServer(sslOptions, (req, res) => {
    const options = {
        hostname: 'localhost',
        port: TARGET_PORT,
        path: req.url,
        method: req.method,
        headers: req.headers
    };

    const proxyReq = http.request(options, (proxyRes) => {
        res.writeHead(proxyRes.statusCode, proxyRes.headers);
        proxyRes.pipe(res, { end: true });
    });

    proxyReq.on('error', (err) => {
        console.error('Proxy Error:', err.message);
        res.writeHead(502);
        res.end('Bad Gateway: PHP Server is not running on port ' + TARGET_PORT);
    });

    req.pipe(proxyReq, { end: true });
});

server.listen(PROXY_PORT, () => {
    console.log(`HTTPS Proxy is running on https://localhost:${PROXY_PORT}`);
    console.log(`Forwarding to PHP server on http://localhost:${TARGET_PORT}`);
});
