import React, { Component } from 'react';
import LoginService from '../../services/LoginService';

class Login extends Component {
    constructor(props) {
        super(props);

        this.state = {
            hasError: false,
            errorMessage: ''
        };

        this.handleLoginFormSubmit = this.handleLoginFormSubmit.bind(this);
    }

    handleLoginFormSubmit(event) {
        event.preventDefault();
        this.props.setGlobalLoaderState(true);
        LoginService.login(event.target.username.value, event.target.password.value).then(() => {
            this.setState({
                hasError: false
            });
            this.props.handleLoginChange(true);
        }).catch(reason => {
            this.setState({
                hasError: true,
                errorMessage: reason.response.data.error
            });
            this.props.handleLoginChange(false);
        });
    }

    render() {
        const { hasError, errorMessage } = this.state;
        let errorElement = '';
        if (hasError) {
            errorElement = <span className="form-text text-muted alert alert-danger">{errorMessage}</span>;
        }

        return (
            <form className="form-inline" onSubmit={this.handleLoginFormSubmit}>
                <div className="form-group">
                    <label htmlFor="email">Email:</label>
                    <input className="form-control" type="email" name="username" id="email" />
                    <label htmlFor="password">Password:</label>
                    <input className="form-control" type="password" name="password" id="password" />
                </div>
                <button type="submit" className="btn btn-primary">Login</button>
                {errorElement}
            </form>
        );
    }
}

export default Login;