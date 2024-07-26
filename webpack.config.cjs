const path = require('path');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const WebpackRTLPlugin = require('@automattic/webpack-rtl-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');

module.exports = (env, argv) => {
  const mode = argv.mode === 'production' ? 'production' : 'development';
  return {
    mode,
    entry: {
      theme: './src/assets/scss/theme.scss',
      user: './src/assets/scss/user.scss'
    },
    output: {
      path: path.resolve(__dirname, 'public/css')
    },
    plugins: [
      new FixStyleOnlyEntriesPlugin(),
      new MiniCssExtractPlugin({
        filename: '[name].min.css'
      }),
      new WebpackRTLPlugin(),
      new CleanWebpackPlugin()
    ],
    devtool: 'source-map',
    module: {
      rules: [
        {
          test: /\.(sass|scss)$/,
          use: [
            MiniCssExtractPlugin.loader,
            {
              loader: 'css-loader',
              options: {
                sourceMap: true,
                url: false
              }
            },
            {
              loader: 'sass-loader',
              options: {
                sourceMap: true
              }
            }
          ]
        }
      ]
    }
    // optimization: {
    //   minimize: true,
    //   minimizer: [new CssMinimizerPlugin()]
    // }
  };
};
