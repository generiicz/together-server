'use strict'

const mongoose = require('mongoose')
const crypto = require('crypto')
const utils = require.main.require('./server/helpers/utils')


const RefreshTokenSchema = new mongoose.Schema({
    token: { type: String, required: true },
    clientID: { type: String, required: true },
    userID: { type: String, required: true }
})

RefreshTokenSchema.statics.generate = function generate(clientID, userID, callback) {
    function saveCallback(error) {
        if (error) { return callback(error) }
        return callback(null, { token: token })
    }

    let newToken = new this()
    const token = utils.uid(256)
    const tokenHash = this.createHash(token)

    newToken.token = tokenHash
    newToken.clientID = clientID
    newToken.userID = userID

    newToken.save(saveCallback)
}

RefreshTokenSchema.statics.createHash = function createHash(token) {
    return crypto.createHash('sha1').update(token).digest('hex')
}

RefreshTokenSchema.statics.hashAndFindOne = function hashAndFindOne(token, callback) {
    const tokenHash = this.createHash(token)
    this.findOne({token: tokenHash}, callback)
}

module.exports = mongoose.model('RefreshToken', RefreshTokenSchema)