'use strict'

const mongoose = require('mongoose')
const crypto = require('crypto')
const utils = require.main.require('./server/helpers/utils')
const expirationDateFunc = require.main.require('./server/helpers/expiration-date')
const lifeTime = require.main.require('./config/env').accessTokenTTL

const AccessTokenSchema = new mongoose.Schema({
    token: { type: String, required: true },
    clientID: { type: String, required: true },
    userID: { type: String, required: true },
    expirationDate: { type: Date, required: true }
})

AccessTokenSchema.index({expirationDate: 1}, {expireAfterSeconds:0})

AccessTokenSchema.statics.generate = function generate(clientID, userID, callback) {
    function saveCallback(error) {
        if (error) { return callback(error) }
        return callback(null, { token: token, expires: newToken.expirationDate })
    }

    let newToken = new this()
    const token = utils.uid(256)
    const tokenHash = this.createHash(token)
    const expirationDate = expirationDateFunc(lifeTime)
    
    newToken.token = tokenHash
    newToken.expirationDate = expirationDate
    newToken.clientID = clientID
    newToken.userID = userID

    newToken.save(saveCallback)
}

AccessTokenSchema.statics.createHash = function createHash(token) {
    return crypto.createHash('sha1').update(token).digest('hex')
}

AccessTokenSchema.statics.hashAndFindOne = function hashAndFindOne(token, callback) {
    const tokenHash = this.createHash(token)
    this.findOne({token: tokenHash}, callback)
}

module.exports = mongoose.model('AccessToken', AccessTokenSchema)