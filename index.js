const mongoose = require('mongoose')
const util = require('util')
const config = require('./config/env')
const app = require('./config/express')

const debug = require('debug')('together-api:index')

mongoose.Promise = Promise
mongoose.connect(config.db, { server: { socketOptions: { keepAlive: 1 } } })

if (config.MONGOOSE_DEBUG) {
  mongoose.set('debug', (collectionName, method, query, doc) => {
    debug(`${collectionName}.${method}`, util.inspect(query, false, 20), doc)
  })
}

app.listen(config.port, () => {
  debug(`server started on port ${config.port} (${config.env})`)
})

module.exports = app