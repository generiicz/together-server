
const swaggerJSDoc = require('swagger-jsdoc')
const config = require.main.require('./config/env')

module.exports = () => {
    const swaggerDefinition = {
    info: {
        title: 'Together API',
        version: '0.1.0',
    },
        host: `localhost:${config.port}`,
        basePath: config.basePath
    }

    const options = {
        swaggerDefinition: swaggerDefinition,
        apis: ['./server/routes/*.js', './server/models/*.js']
    } 

    return swaggerJSDoc(options)
}