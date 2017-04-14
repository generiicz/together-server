'use strict'

const mongoose = require('mongoose')
const bcrypt = require('bcrypt-nodejs')
const shortid = require('shortid')
const ENUMS = require('./enums')
const _ = require('lodash')

const SALT_WORK_FACTOR = 5

/*
    ENUMS
*/



const UserSchema = new mongoose.Schema({

    // Required fields

    _id: {
        type: String,
        default: shortid.generate,
        unique: true,
        required: true
    },

    username: { 
        type: String, 
        required: true, 
        unique: true 
    },

    email: {
        type: String, 
        required: true, 
        unique: true 
    },

    password: { 
        type: String, 
        required: true 
    },

    createdAt: {
        type: Date,
        default: Date.now,
        required: true
    },

    location: {
        type: String 
    },

    interests: [{
        id: {
            type: String,
            required: true
        }
    }],

    // Optional fields

    gender: {
        type: String,
        default: ENUMS.GENDER.NOT_STATED,
        enum: _.values(ENUMS.GENDER)
    },

    birthday: {
        type: Date
    },

    friends: [{
        id: {
            type: String,
            required: true
        }
    }],

    aboutMe: {
        type: String
    },

    events: [{
        id: {
            type: String,
            required: true
        }
    }],

    photos: [{
        id: {
            type: String,
            required: true
        }
    }],

    education: {
        type: String,
        enum: _.values(ENUMS.EDUCATION),
        default: ENUMS.EDUCATION.NOT_STATED
    },

    occupation: {
        type: String
    },

    relationshipStatus: {
        type: String,
        enum: _.values(ENUMS.RELATIONSHIP_STATUS),
        default: ENUMS.RELATIONSHIP_STATUS.NOT_STATED
    },

    allowOthersToSeeMyEvents: {
        type: Boolean
    }

})

UserSchema.pre('save', function(next) {

    function genSaltCallback(error, salt) {
        if (error) { return next(error) }
        bcrypt.hash(user.password, salt, null, hashCallback)
    }

    function hashCallback(error, hash) {
        if (error) { return next(error) }
        user.password = hash
        return next()
    }

    const user = this

    if (!user.isModified('password')) { return next() }
    bcrypt.genSalt(SALT_WORK_FACTOR, genSaltCallback)
})

UserSchema.methods.verifyPassword = function verifyPassword(password, callback) {
    function compareCallback(error, isMatch) {
        if (error) { return callback(error) }
        callback(null, isMatch)
    }

    bcrypt.compare(password, this.password, compareCallback)
}

UserSchema.statics.emailAvailability = function chechkEmailAvailability(email, callback) {
    function findOneCallback(error, user) {
        if (error) { return callback(error) }
        if (user) { return callback(null, false) }
        return callback(null, true)
    }

    this.findOne({ email: email }, findOneCallback)
}

UserSchema.statics.usernameAvailability = function usernameAvailability(username, callback) {
    function findOneCallback(error, user) {
        if (error) { return callback(error) }
        if (user) { return callback(null, false) }
        return callback(null, true)
    }

    this.findOne({ username: username }, findOneCallback)
}

module.exports = mongoose.model('User', UserSchema)