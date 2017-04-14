'use strict'

const mongoose = require('mongoose')
const shortid = require('shortid')
const _ = require('lodash')

const EventSchema = new mongoose.Schema({
    
    _id: {
        type: String,
        default: shortid.generate,
        unique: true,
        required: true
    },

    creator: {
        type: String,
        required: true
    },

    createdAt: {
        type: Date,
        default: Date.now,
        required: true
    },

    lastUpdate: {
        type: Date,
        default: Date.now,
        required: true
    },

    title: {
        type: String,
        required: true
    },

    attenders: [{
        id: {
            type: String,
            required: true,
            unique: true
        }
    }],

    // TODO: EXTRA TICKETS
    // TODO: INVITES
    // TODO: REQUESTS

    coordinates: {
        type: Point,
        required: true
    },

    placeName: {
        type: String,
        required: true
    },

    date: {
        type: Date,
        required: true
    },

    // TODO: TYPE 

    photos: [{
        id: {
            type: String,
            required: true
        }
    }],

    isPrivate: {
        type: Boolean,
        required: true
    },

    availableTo: [{
        userId: {
            type: String,
            required: true
        }
    }],
    
    description: {
        type: String,
        required: true
    },


})

module.exports = mongoose.model('Event', EventSchema)