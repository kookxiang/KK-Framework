const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require("extract-text-webpack-plugin");

var options = {
  entry: {
    Demo: './Resource/Demo/test.js',
    Error: './Resource/Misc/Error.css'
  },
  resolve: {
    alias: {
      'react': 'react-lite',
      'react-dom': 'react-lite'
    },
    extensions: [".js", ".jsx"]
  },
  module: {
    rules: [
      {
        test: /\.(sa|sc|c)ss$/,
        use: ExtractTextPlugin.extract({
          fallback: "style-loader",
          use: [
            "css-loader",
            {
              loader: "postcss-loader",
              options: {
                plugins: [
                  require("autoprefixer")({ browsers: ["ie >= 9", "> 2%", "last 1 version"] })
                ]
              }
            },
            "sass-loader"
          ]
        })
      }, {
        test: /\.(jpe?g|gif|png|svg|woff\d*|ttf|eot)(\?.*|#.*)?$/,
        use: "file-loader?limit=8192&name=Assert/[hash:hex:6].[ext]"
      }, {
        test: /\.js|jsx$/,
        exclude: /(node_modules|bower_components)/,
        loader: "babel-loader",
        options: {
          presets: ["es2015", "react"]
        }
      }
    ]
  },
  output: {
    path: path.resolve(__dirname, "Public/Resource"),
    filename: '[name].js',
    publicPath: '/Resource/'
  },
  plugins: [
    new ExtractTextPlugin("[name].css")
  ]
};

module.exports = function(env){
  return require('./.webpack/' + (env == 'prod' ? 'prod' : 'dev') + '.js')(options);
};
