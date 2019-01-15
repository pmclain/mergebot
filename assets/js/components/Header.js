import React, { Component } from 'react';
import Login from './header/Login';

class Header extends Component {
    render() {
        const { isLoggedIn, handleLoginChange } = this.props;
        let content;

        if (isLoggedIn) {
            content = '';
        } else {
            content = <Login handleLoginChange={handleLoginChange} setGlobalLoaderState={this.props.setGlobalLoaderState}/>
        }

        return (
            <header className="row">
                {content}
            </header>
        );
    }
}

export default Header;