const express = require('express');
const app = express();

// Puerto 80 en Docker, puerto 3000 para pruebas locales (PORT=3000).
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

app.get('/', (req, res) => {
  const number = Math.floor(Math.random() * phrases.length);
  res.type('text/plain; charset=utf-8').send(phrases[number]);
});

app.listen(port, '0.0.0.0', () => {
  console.log(`Random Quotes app listening on port ${port}`);
});
