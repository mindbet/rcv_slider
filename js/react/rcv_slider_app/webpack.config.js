const path = require('path');

module.exports = env => {
  console.log('NODE_ENV: ', env.NODE_ENV); // 'local'
  console.log('Production: ', env.production); // true

  return {
    entry: './src/index.js',
    resolve: { extensions: ['.js', '.jsx'] },
    mode: (process.env.NODE_ENV === 'production') ? 'production' : 'development',
    devtool: (env.production) ? false : 'inline-source-map',
    output: {
      path: path.resolve(__dirname, 'dist'),
      filename: 'app.bundle.js'
    },
    module: {
      rules: [
        {
          test: /\.(js|jsx)$/,
          exclude: /(node_modules)/,
          use: {
            loader: 'babel-loader',
            options: {
              presets: [
                "@babel/preset-env",
                "@babel/preset-react",{
                  'plugins': ['@babel/plugin-proposal-class-properties']}]
            }
          }
        },
        {
          test: /\.css$/i,
          use: ['css-loader'],
        }
      ]
    },
  }
};

// module.exports = config;
