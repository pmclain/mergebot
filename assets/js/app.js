import React from 'react';
import ReactDOM from 'react-dom';
import App from './components/App';

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');

const render = () => {
    ReactDOM.render(
        <App />,
        document.getElementById('root')
    )
};

render();
