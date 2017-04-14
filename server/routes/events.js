'use strict'

const express = require('express')
const router = express.Router()
const controller = require('../controllers/events')

router.route('/')
    .post(notImplemented) 

router.route('/:id')
    .get(notImplemented)
    .patch(notImplemented)


function notImplemented(req, res) {
    res.sendStatus(501)
}

module.exports = router