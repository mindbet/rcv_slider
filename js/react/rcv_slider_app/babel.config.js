module.exports = {
  presets: [
    [
      '@babel/preset-env',
      {
        targets: [
          'last 1 chrome version',
          'last 1 safari version',
          'last 1 firefox version',
        ].join(', '),
      },
    ],
  ],
  // ...
}
