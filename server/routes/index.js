'use strict'

const express = require('express')
const swagger = require.main.require('./server/helpers/swagger')
const userCtrl = require('./users')
const eventCtrl = require('./events')
const authCtrl = require('./auth')

const router = express.Router()
const swaggerSpec = swagger()

router.get('/swagger.json', (req, res) => {
    res.setHeader('Content-Type', 'application/json')
    res.send(swaggerSpec)
})

/** GET /api/{v}/health-check - Check service health */
router.get('/health-check', (req, res) => res.send('OK'))

router.use('/users', userCtrl)
router.use('/events', eventCtrl)
router.use('/auth', authCtrl)

module.exports = router
