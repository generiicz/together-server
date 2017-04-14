'use strict'

const mongoose = require('mongoose')

const ClientSchema = new mongoose.Schema({
})

module.exports = mongoose.model('Client', ClientSchema)