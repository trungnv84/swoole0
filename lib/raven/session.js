const PORT = 55555;
const express = require('express');
const bodyParser = require('body-parser');
const app = express();
app.use(bodyParser());

app.get('/', (req, res) => {
    res.send('');
});

app.get('/ping', async (req, res) => {
    res.send('1');
});

var store = false;

app.post('/open', async (req, res) => {
    if (!store) {
        // zzz
        const authOptions = {
            certificate: fs.readFileSync(CONFIG.ravendb.certificate.file),
            type: CONFIG.ravendb.certificate.type,
            password: CONFIG.ravendb.certificate.password,
        };
        store = new DocumentStore(CONFIG.ravendb.url, CONFIG.ravendb.database, authOptions);
        store.initialize();
    }
    res.send('1');
});

app.listen(PORT, () => {
    console.log(`App listening at http://localhost:${PORT}`)
});