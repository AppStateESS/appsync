import React from 'react-dom/node_modules/react';
import ReactDOM from 'react-dom';
import AppBox from './organization.jsx'

main();

function main(){

    ReactDOM.render(
        <AppBox/>,
        document.getElementById('org')
    );
}
