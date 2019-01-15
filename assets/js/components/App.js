import React, { Component } from 'react';
import Header from './Header';
import Log from './Log';
import Loader from 'react-loader-spinner';

class App extends Component {
    constructor(props) {
        super(props);

        this.state = {
            loading: true,
            isLoggedIn: true
        };

        this.handleLoginChange = this.handleLoginChange.bind(this);
        this.setGlobalLoaderState = this.setGlobalLoaderState.bind(this);
    }

    handleLoginChange(loginState) {
        this.setState({
            isLoggedIn: loginState
        });
    }

    setGlobalLoaderState(newState) {
        this.setState({
            loading: newState
        });
    }

    render() {
        const { isLoggedIn, loading } = this.state;
        let log = '',
            loader = '';
        if (isLoggedIn) {
            log = <Log handleLoginChange={this.handleLoginChange} setGlobalLoaderState={this.setGlobalLoaderState}/>
        }
        if (loading) {
            loader = <div className="loader loader__full-page"><Loader type="Circles"/></div>;
        }

        return (
            <div className="container">
                <Header isLoggedIn={isLoggedIn} handleLoginChange={this.handleLoginChange} setGlobalLoaderState={this.setGlobalLoaderState}/>
                {loader}
                {log}
            </div>
        );
    }
}

export default App;