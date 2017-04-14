const Joi = require('joi')

module.exports = {
    // POST /api/{v}/users
    createUser: {
        body: {
            username: Joi.string().required(),
            email: Joi.string().email().required(),
            password: Joi.string().required()
        }
    }
}