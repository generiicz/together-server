'use strict'

const oauth2orize = require('oauth2orize')
const passport = require('passport')
const async = require('async')

const User = require('../models/user')
const AccessToken = require('../models/tokens/access-token')
const RefreshToken = require('../models/tokens/refresh-token')
const auth = require('../controllers/auth')

const server = oauth2orize.createServer()

// Resource owner password
server.exchange(oauth2orize.exchange.password(passwordFlow))
function passwordFlow(client, username, password, scope, done) {
    function parallelCallback(error, result) {
        if (error) { return done(error) }
        const accessToken = result.accessToken.token
        const refreshToken = result.refreshToken.token
        const expires = result.accessToken.expires
        return done(null, accessToken, refreshToken, {expires: expires})
    }

    function findOneCallback(error, doc) {
        if (error) { return done(error) }
        if (!doc) { return done(null, false) }
        user = doc
        user.verifyPassword(password, verifyCallback)
    }

    function verifyCallback(error, isMatch) {
        if (error) { return done(error) }
        if (!isMatch) { return done(null, false) }
        const stack = {}
        const clientID = client._id
        const userID = user._id

        stack.accessToken = function(callback) { AccessToken.generate(clientID, userID, callback) }
        stack.refreshToken = function(callback) { RefreshToken.generate(clientID, userID, callback) }
        async.parallel(stack, parallelCallback)
    }
    let user
    User.findOne({ username: username }, findOneCallback)
}

// Refresh token
server.exchange(oauth2orize.exchange.refreshToken(refreshTokenFlow))
function refreshTokenFlow(client, refreshToken, scope, done) {
    function generateCallback(error, result) {
        if (error) { return done(error) }
        const accessToken = result.token
        const expires = result.expires
        return done(null, accessToken, refreshToken, {expires: expires})
    }

    function findOneCallback(error, token) {
        if (error) { return done(error) }
        if (!token) { return done(null, false) }
        if (client.clientID !== token.clientID) { return done(null, false) }
        AccessToken.generate(clientID, userID, generateCallback)
    }

    RefreshToken.hashAndFindOne(refreshToken, findOneCallback)
}

exports.resourceOwnerToken = [
    auth.isResourceOwnerAuthenticated, 
    server.token(),
    server.errorHandler()
]