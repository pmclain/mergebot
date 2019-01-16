import React, { Component } from 'react';
import LoginService from '../../services/LoginService';

class Detail extends Component {
    constructor(props) {
        super(props);

        this.state = {
            open: false,
        };

        this.toggleOpen = this.toggleOpen.bind(this);
    }

    toggleOpen() {
        this.setState({
            open: !this.state.open,
        });
    }

    render() {
        const { open } = this.state;
        const { event } = this.props;

        return (
            <div className={open ? 'detail__open' : 'detail__closed'}>
                <button className="toggle" onClick={this.toggleOpen}>&lt;</button>
                <span>{JSON.stringify(event)}</span>
                <pre>{JSON.stringify(event, null, 2) }</pre>
            </div>
        );
    }
}

export default Detail;