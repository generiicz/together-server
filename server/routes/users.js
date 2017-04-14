'use strict'

const express = require('express')
const validate = require('express-validation')
const paramValidation = require.main.require('./config/param-validation')
const userCtrl = require('../controllers/users')

const router = express.Router()

router.route('/')
    .post(validate(paramValidation.createUser), userCtrl.signup)

router.route('/logout')
    .post(userCtrl.logout)

router.route('/check/:field/:value')
    .get(userCtrl.availability)

module.exports = router