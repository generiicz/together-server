module.exports = {
    env: 'development',
    MONGOOSE_DEBUG: true,
    db: 'mongodb://admin:admin@ds163387.mlab.com:63387/togetherdb',
    port: 3000,
    accessTokenTTL: { hours: 24, minutes: 0, seconds: 0 },
    clientSecret: '123123123',
    clientID: '57c54a23f36d2866ee356c16',
    basePath: '/api/v1'
}