'use strict'

const async = require('async') 
const User = require('../models/user')

// Middleware
exports.doesUserExist = function(req, res, next) {
    function findByIdCallback(error, user) {
        if (error) { return res.status(500).json(error) }
        if (!user) {
            res.statusMessage = "Requested user doesn't exist."
            return res.status(404).end()
        }
        req.requestedUser = user
        return next()
    }

    const id = req.params.userid
    if (!id) { return res.status(422).end() }
    return User.findById(id, findByIdCallback)
}

exports.getUserInfo = function(req, res, next) {
    return res.status(501).end()
}

// Methods
exports.signup =  function(req, res) {
    function saveCallback(error, user) {
        if (error) { return res.status(500).json(error) }
        user.password = undefined
        user['__v'] = undefined
        return res.json(user)
    }

    function parallelCallback(error, result) {
        if (error) { return res.status(500).json(error) }
        const emailAvailable = result.email
        const usernameAvailable = result.username

        if (!emailAvailable || !usernameAvailable) {
            res.statusMessage = 'Email or username is not available'
            return res.status(409).end()
        }

        const user = new User({
            username: username,
            email: email,
            password: password
        })

        return user.save(saveCallback)
    }

    const username = req.body.username
    const email = req.body.email 
    const password = req.body.password

    const checks = {}
    checks.email = function(callback) { User.emailAvailability(email, callback) }
    checks.username = function(callback) { User.usernameAvailability(username, callback) }
    return async.parallel(checks, parallelCallback)
}

exports.availability = function(req, res) {
    function checkCallback(error, isAvailable) {
        if (error) { return res.status(500).json(error) }
        return res.json({ available: isAvailable })
    }

    const field = req.params.field
    const value = req.params.value

    switch (field) {
        case 'email': User.emailAvailability(value, checkCallback); break
        case 'username': User.usernameAvailability(value, checkCallback); break
        default: res.sendStatus(404)
    }
}

exports.logout = function(req, res) {
    // TODO: remove tokens
}

exports.search = function(req, res) {
    function findCallback(error, users) {
        if (error) { return res.status(500).json(error) }
        return res.json(users)
    }

    const username = req.query.username
    if (!username || username.length < 3) { return res.status(422).end() }
    return User.find({ username: new RegExp('^'+username, 'i') }, findCallback)
        .select('_id username')
}