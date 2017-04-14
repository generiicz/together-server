'use strict'

const express = require('express')
const validate = require('express-validation')
const oauthCtrl = require('../controllers/oauth')
const paramValidation = require.main.require('./config/param-validation')

const router = express.Router()

/** POST /api/{v}/auth/token - Returns token if correct username and password is provided */
router.route('/token')
    .post(oauthCtrl.resourceOwnerToken)

module.exports = router
