const express = require('express');
const bodyParser = require('body-parser');
const nodemailer = require('nodemailer');

const app = express();
const port = 3000;

app.use(bodyParser.json());

// Endpoint to receive emails
app.post('/receive-email', (req, res) => {
  // Use nodemailer to receive and process emails
  // Example code to extract sender's email address
  const senderEmail = req.body.sender;
  
  // Save senderEmail to a database or process it as needed

  console.log('Received email from:', senderEmail);

  res.send('Email received successfully');
});

app.listen(port, () => {
  console.log(`Server running at http://localhost:${port}`);
});
