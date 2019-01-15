import React, { Component } from 'react';
import Table from 'react-table';
import EventService from '../services/EventService';
import Detail from './log/Detail';
import 'react-table/react-table.css';

class Log extends Component {
    constructor(props) {
        super(props);

        this.state = {
            loading: true,
            events: []
        };
    }

    componentDidMount() {
        if (this.state.loading === false) {
            return;
        }

        EventService.getEvents().then(result => {
            this.props.setGlobalLoaderState(false);
            this.setState({
                loading: false,
                events: result.data
            });
        }).catch(reason => {
            if (reason.response.status === 401) {
                this.props.handleLoginChange(false);
                this.props.setGlobalLoaderState(false);
            }
        });
    }

    render() {
        const { events } = this.state;
        const columns = [
            {
                Header: 'ID',
                accessor: 'id',
                maxWidth: 75,
            },
            {
                Header: 'Task Name',
                accessor: 'task_name',
                maxWidth: 150,
            },
            {
                Header: 'Created',
                accessor: 'created_at',
                maxWidth: 200,
                Cell: row => {
                    return row.original.created_at.date;
                }
            },
            {
                Header: 'Event Data',
                accessor: 'event_data',
                Cell: row => <Detail event={row.original.event_data}/>,
            }
        ];

        return (
            <Table data={events} columns={columns}/>
        );
    }
}

export default Log;