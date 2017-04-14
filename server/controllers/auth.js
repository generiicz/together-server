'use strict'

const passport = require('passport')
const BasicStrategy = require('passport-http').BasicStrategy
const ClientPasswordStrategy = require('passport-oauth2-client-password').Strategy
const BearerStrategy = require('passport-http-bearer').Strategy

const Client = require('../models/client')
const AccessToken = require('../models/tokens/access-token')
const User = require('../models/user')
const appClientSecret = require.main.require('./config/env').clientSecret

passport.use('resource-owner-client-basic', new BasicStrategy(passwordStrategyHandler))
passport.use('resource-owner-client-password', new ClientPasswordStrategy(passwordStrategyHandler))
passport.use('resource-owner-access-token', new BearerStrategy(bearerStrategyHandler))

function passwordStrategyHandler(clientID, clientSecret, done) {
    function findByIdCallback(error, client) {
        if (error) { return done(error) }
        if (!client) { return done(null, false) }
        if (clientSecret == appClientSecret) { return done(null, client) }
        else return done(null, false)
    }
    Client.findById(clientID, findByIdCallback)
}

function bearerStrategyHandler(accessToken, done) {
    function findByIDCallback(error, user) {
        if (error) { return done(error) }
        if (!user) { return done(null, false) }
        return done(null, user)
    }

    function findOneCallback(error, token) {
        if (error) { return done(error) }
        if (!token) { return done(null, false) }
        if (new Date() > token.expirationDate) { return done(null, false) }
        User.findById(token.userID, findByIDCallback)
    }

    AccessToken.hashAndFindOne(accessToken, findOneCallback)
}

exports.isResourceOwnerAuthenticated = passport.authenticate(['resource-owner-client-basic', 'resource-owner-client-password'], { session : false });
exports.isResourceOwnerAccessToken = passport.authenticate('resource-owner-access-token', { session : false });


