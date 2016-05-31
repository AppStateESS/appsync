import React from 'react';
import ReactDOM from 'react-dom';

export default class LogBox extends React.Component {
  render() {
    return (<LogViewBox/>);
  }
}

// Component responsible for the logic behind the log display as well as communication
// with the server.
var LogViewBox = React.createClass({
    // Sets up an initial state for the class, with default values.
    getInitialState: function()
    {
        return {
                    log         : [],
                    retrieved   : false,
                    users       : [],
                    filter      : false,
                    username    : ""
        }
    },
    // When the component mounts retrieve the log and the users.
    componentWillMount: function()
    {
        this.getLog();
        this.getUsernames();
    },
    // Toggles the filter value
    filterToggle: function()
    {
        this.setState({
                            filter  : !(this.state.filter)
        });
    },
    // Changes the username to the one in users array corresponding with the passed
    // in index.
    changeUsername: function(index)
    {
        if(index == -1)
        {
            this.setState({
                            username    : ""
            });
        }
        else
        {
            this.setState({
                            username    : this.state.users[index].username
            });
        }
    },
    // Retrieves the log from the server via AJAX.
    getLog: function()
    {
        $.ajax({
            url         : 'index.php?module=appsync&action=AjaxGetLog',
            type        : 'GET',
            dataType    : 'json',
            success: function(data)
            {
                this.setState({
                                    log         : data,
                                    retrieved   : true
                });
            }.bind(this),
            error: function(xhr, status, err)
            {
                //TODO create error handler
            }.bind(this)
        });
    },
    // Retrieves the usernames from the server via AJAX.
    getUsernames: function()
    {
        $.ajax({
            url         : 'index.php?module=appsync&action=AjaxRetrieveUsers',
            type        : 'GET',
            dataType    : 'json',
            success: function(data)
            {
                this.setState({
                                    users   : data
                });
            }.bind(this),
            error: function(xhr, status, err)
            {
                //TODO create error handler
            }.bind(this)
        })
    },
    // Render function
    render: function()
    {
        // Set up the variables
        var log;
        var rows;
        var data = this.state.log;

        // If the state is retrieved then display the data retrieved
        if(this.state.retrieved)
        {
            var empty = true;

            if(this.state.username != "")
            {
                var checkUsername = this.state.username;

                var rows = data.map(function(node){
                    if(node.username == checkUsername)
                    {
                        empty = false;

                        return (
                            <tr key={node.occurredOn}>
                                <td>
                                    {node.occurredOn}
                                </td>
                                <td>
                                    {node.username}
                                </td>
                                <td>
                                    {node.desc}
                                </td>
                            </tr>);
                    }
                });
            }
            else
            {
                var rows = data.map(function(node){
                    empty = false;

                    return (
                        <tr>
                            <td>
                                {node.occurredOn}
                            </td>
                            <td>
                                {node.username}
                            </td>
                            <td>
                                {node.desc}
                            </td>
                        </tr>);
                });
            }

            // In case there are no log entries, an empty message
            if(empty)
            {
                rows = (
                    <p>No log entries</p>
                );
            }

            // Finally create the log table to be displayed
            log = (
                <table className="table table-hover table-striped">
                    <thead>
                        <th>Occurred</th>
                        <th>Username</th>
                        <th>Description</th>
                    </thead>
                    <tbody>
                        {rows}
                    </tbody>
                </table>
            );
        }
        else
        {
            log = (
                <div></div>
            );
        }

        return (
                <div>
                    <h2>Change Log</h2>
                    <div className="col-md-9">
                        <FilterBox filterToggle={this.filterToggle}
                                   changeUsername={this.changeUsername}
                                   filter={this.state.filter}
                                   users={this.state.users} />
                        {log}
                    </div>
                </div>
        );
    }
});

// Component responsible for the logic behind setting the filter
var FilterBox = React.createClass({
    // On click function that calls the parent's filterToggle method.
    filterToggle: function()
    {
        this.props.filterToggle();
    },
    // An on change function responsible for passing the changed value up to the parent component.
    changeFilterUsername: function()
    {
        var uChoice = ReactDOM.findDOMNode(this.refs.usernameChoice);
        var value   = uChoice.value;

        this.props.changeUsername(value);
    },
    // Render Function
    render: function()
    {
        // Create several useful variables
        var usernameInput;
        var usernames;
        var users = this.props.users;
        var data = new Array();

        // Set the default option that will be displayed first in the dropdown
        var defaultOption = {
                                username    : "All Users"
        };

        // Push the defaultOption onto the array to be the first option in the dropdown
        data.push(defaultOption);

        // Loop through and add each user in order
        var i = 0;
        for(i; i< users.length;i++)
        {
            data.push(users[i]);
        }

        // If the filter is set then display the dropdown to select the username to filter by
        if(this.props.filter)
        {
            var x = -1;

            usernames = data.map(function(node){
                var option = (
                    <option value={x}>
                        {node.username}
                    </option>
                );

                x++;

                return option;
            });

            usernameInput = (
                                <div className="form-group">
                                    <label>Username</label>
                                    <select onChange={this.changeFilterUsername} ref="usernameChoice">
                                        {usernames}
                                    </select>
                                </div>
            );
        }
        else {
            usernameInput = (
                <div></div>
            );
        }

        return(
            <div>
                <div className="checkbox">
                    <label>
                        <input onClick={this.filterToggle} type="checkbox" /> Filter by Username
                    </label>
                </div>
                {usernameInput}
            </div>
        );

    }
});
