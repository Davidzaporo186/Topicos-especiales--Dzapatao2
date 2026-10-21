const express = require('express');
const os = require('node:os');

const app = express();
const port = Number(process.env.PORT || 80);

const phrases = [
  'Get ready to be inspired…',
  'See rejection as redirection.',
  'There is beauty in simplicity.',
  'You can’t be late until you show up.',
  'Maybe life is testing you. Don’t give up.',
  'Impossible is just an opinion.',
  'Alone or not you gonna walk forward.'
];

app.get('/', (_req, res) => {
  const number = Math.floor(Math.random() * phrases.length);
  res.type('text/plain').send(`${phrases[number]} - Container Id: ${os.hostname()}`);
});

app.get('/health', (_req, res) => res.json({ status: 'ok', container: os.hostname() }));

app.listen(port, '0.0.0.0', () => {
  console.log(`Randomquotes escuchando en el puerto ${port}`);
});
