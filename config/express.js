const express = require('express')
const logger = require('morgan')
const path = require('path')
const bodyParser = require('body-parser')
const expressValidation = require('express-validation')
const mongoose = require('mongoose')
const passport = require('passport')
const helmet = require('helmet')
const util = require('util')

const router = require.main.require('./server/routes')
const config = require('./env')

const app = express()

if (config.env === 'development') {
  app.use(logger('dev'))
}

app.use(bodyParser.json())
app.use(bodyParser.urlencoded({ extended: false }))
app.use(passport.initialize())
app.use(helmet())
app.use(express.static(path.join(__dirname, 'public')));


app.use(config.basePath, router)

module.exports = app
