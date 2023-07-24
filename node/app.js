const express = require('express');
const cors = require('cors');
const moment = require('moment');
const fs = require('fs');
const csvParser = require('csv-parser');
const axios = require('axios');

const app = express();
const PORT = 5000;
const CSV_FILE_PATH = '/home/eduardo/Desktop/Pantanal_Dev/Onca_Pintada/kaggle_dataset_credit_card_fraud_detection/creditcard.csv';
app.use(cors());


let linhasAleatorias = [];
let quantidadeFraudes = 0;
let Frauds = [];

// Função para ler o arquivo CSV e armazenar as linhas aleatórias em uma variável global
const carregarLinhasAleatorias = () => {
linhasAleatorias = [];
return new Promise((resolve, reject) => {
    fs.createReadStream(CSV_FILE_PATH)
    .pipe(csvParser())
    .on('data', (linha) => linhasAleatorias.push(linha))
    .on('end', () => resolve())
    .on('error', (error) => reject(error));
});
};

// Rota para enviar uma linha aleatória do arquivo CSV
app.get('/transacoes', (req, res) => {
    if (linhasAleatorias.length === 0) {
      res.status(500).json({ error: 'O arquivo CSV não foi carregado ainda.' });
      return;
    }
    const linhaAleatoria = linhasAleatorias[Math.floor(Math.random() * linhasAleatorias.length)];
    let linhaAleatoriaArray = [Object.values(linhaAleatoria).slice(0, -1)].toString();
    linhaAleatoriaArray = `[[${linhaAleatoriaArray}]]`;

    axios.post('http://localhost:5500/classify', linhaAleatoriaArray)
      .then((response) => {
        // console.log('Resposta da porta 5500:', response.data);
  
        Object.keys(linhaAleatoria).forEach((key) => {
          linhaAleatoria[key] = parseFloat(linhaAleatoria[key]);
        });
        if(response.data == 1){
          quantidadeFraudes += 1;
          Frauds.push(new Fraud(quantidadeFraudes, linhaAleatoria));
          console.log(Frauds);
        } 
        
        linhaAleatoria.Time = moment(linhaAleatoria.Time * 1000).format('HH:mm:ss');
        linhaAleatoria.Class = response.data;
  
        res.json(linhaAleatoria);
      })
      .catch((error) => {
        console.error('Erro ao enviar a linha para a porta 5500:', error.message);
        res.status(500).json({ error: 'Erro ao enviar a linha para a porta 5500' });
      });
  });

app.get('/quantidade-fraudes', (req, res) => {
    res.json({ quantidadeFraudes });
});
  

carregarLinhasAleatorias().then(() => {
// Inicia o servidor na porta definida
app.listen(PORT, () => {
    console.log(`Servidor rodando na porta ${PORT}`);
    });
});

