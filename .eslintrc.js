module.exports = {
  env: {
    browser: true,
    es6: true
  },
  extends: [
    'plugin:react/recommended',
    'standard',
    'prettier',
    "prettier/babel",
    "prettier/react",
    "prettier/standard"
  ],
  globals: {
    Atomics: 'readonly',
    SharedArrayBuffer: 'readonly',
    '$': false,
    '_': false,
    'axios': false,
    'Popper': false,
    'window': false,
  },
  parserOptions: {
    ecmaFeatures: {
      jsx: true
    },
    ecmaVersion: 2018,
    sourceType: 'module'
  },
  plugins: [
    'react',
    'babel',
    'standard',
    'prettier'
  ],
  rules: {
    'standard/no-callback-literal': [2, ['cb', 'callback']],
    'react/jsx-uses-react': 'error',
    'react/jsx-uses-vars': 'error',
  },
  settings: {
    react: {
      'version': 'detect',
      'pragma': 'React'
    }
  }
}
