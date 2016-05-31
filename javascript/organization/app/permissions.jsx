import React from 'react';
import ReactDOM from 'react-dom';
import UmbrellaPickBox from './umbrellaPick.jsx';

export default class PermissionsBox extends React.Component {
  render() {
    return (<PermissionsViewBox umbrellaList={this.props.umbrellaList}/>);
  }
}

var PermissionsViewBox = React.createClass({
    // Sets up an initial state for the class, with default values.
    getInitialState: function()
    {
        return {
                    toggleState : "LIST"
        };
    },
    // Sets the new state and calls certain functions needed to update the rest of the portal.
    changeToggleState: function(newState)
    {
        this.setState({
                            toggleState : newState
        });
    },
    // Render function
    render: function()
    {
        var action;

        if(this.state.toggleState == "LIST")
        {
            action = (<PermissionListBox />);
        }
        else if(this.state.toggleState == "ADD")
        {
            action = (<PermissionAddBox umbrellaList={this.props.umbrellaList}/>);
        }
        else if(this.state.toggleState == "REMOVE")
        {
            action = (<PermissionRemoveBox umbrellaList={this.props.umbrellaList}/>);
        }
        else {
            action = (<div></div>);
        }

        return (
                <div>
                    <h2>
                        Permissions
                    </h2>
                    <PermissionToggleBox state={this.state.toggleState}
                                         toggle={this.changeToggleState}/>
                    {action}
                </div>
        );
    }
});

var PermissionToggleBox = React.createClass({
    // Calls the parent class' toggle function with the LIST flag
    list: function()
    {
        this.props.toggle("LIST");
    },
    // Calls the parent class' toggle function with the ADD flag
    add: function()
    {
        this.props.toggle("ADD");
    },
    // Calls the parent class' toggle function with the REMOVE flag
    remove: function()
    {
        this.props.toggle("REMOVE");
    },
    // Calls the parent class' clear method to attempt to purge all members
    clear: function()
    {
        this.props.clear();
    },
    // Render Function
    render: function()
    {
        // Set the class variables to false initially
        var list    = false;
        var add     = false;
        var remove  = false;

        // Set the appropriate class variable to true
        if(this.props.state == "LIST")
        {
            list = true;
        }
        else if(this.props.state == "ADD")
        {
            add = true;
        }
        else if(this.props.state == "REMOVE")
        {
            remove = true;
        }

        // Set up the list classnames
        var listClasses = classNames({
            'btn'           : true,
            'btn-default'   : true,
            'active'        : list
        });

        // Set up the add classnames
        var addClasses = classNames({
            'btn'           : true,
            'btn-default'   : true,
            'active'        : add
        });

        // Set up the remove classnames
        var removeClasses = classNames({
            'btn'           : true,
            'btn-default'   : true,
            'active'        : remove
        });

        // Add some space on the top by adding a style of marginTop
        var toggleStyle = {marginTop: '25px'};

        return(
            <div className="row" style={toggleStyle}>
                <div className="col-md-6">
                    <div className="btn-group">
                        <label onClick={this.list} className={listClasses}>
                            List Permissions
                        </label>
                        <label onClick={this.add} className={addClasses}>
                            Add Permissions
                        </label>
                        <label onClick={this.remove} className={removeClasses}>
                            Remove Permissions
                        </label>
                    </div>
                </div>
            </div>
        );
    }
});

var PermissionListBox = React.createClass({
    getInitialState: function()
    {
        return ({
                    userPermissions : []
        });
    },
    componentDidMount: function()
    {
        this.retrievePermissions();
    },
    retrievePermissions: function()
    {
        $.ajax({
            url         : 'index.php?module=appsync&action=AjaxGetAllUserPermissions',
            type        : 'GET',
            dataType    : 'json',
            success: function(data)
            {
                this.setState({
                                    userPermissions: data
                });
            }.bind(this),
            error: function(xhr, status, err)
            {
                //TODO create error handler
            }.bind(this)
        });
    },
    // Render Function
    render: function()
    {

        var controlStyle        = {marginTop: '25px'};
        var noPermissionStyle   = {marginTop: '10px'};

        if(this.state.userPermissions.length == 0)
        {
            return (
                <div style={noPermissionStyle}>
                    <p>There are no permissions set at present, to add some permissions click on the Add Permissions tab.</p>
                </div>
            );
        }

        var permissions     = this.state.userPermissions

        var permissionRows  = permissions.map(function(node){
            return (
                <tr key={node.username}>
                    <td>
                        {node.username}
                    </td>
                    <td>
                        {node.permissions}
                    </td>
                </tr>);
        });

        return(
                <div className="row">
                    <div className="col-md-6">
                        <table style={controlStyle} className="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Permissions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {permissionRows}
                            </tbody>
                        </table>
                    </div>
                </div>
        );
    }
});

var PermissionAddBox = React.createClass({
    getInitialState: function()
    {
        return {
                    username        : "",
                    umbrella        : -1,
                    update          : false,
                    umbrellaList    : []
        }
    },
    // When the component mounts update the available portals.
    componentDidMount: function()
    {
        this.getUmbrellas();
    },
    inputChange: function()
    {
        var addUsername = this.refs.usernameInput.value;

        this.setState({
                            username    : addUsername,
                            update      : ""
        });
    },
    setUmbrella: function(umbrellaId)
    {
        this.setState({
                            umbrella    : umbrellaId
        });
    },
    add: function()
    {
        if(confirm("This will give "+this.state.username+" permission to view and alter this umbrella...\n\nAre you sure?"))
        {
            this.addPermission();
        }
    },
    addPermission: function()
    {
        var inputData = {
                            username    : this.state.username,
                            umbrella    : this.state.umbrella
        };

        $.ajax({
            url     : 'index.php?module=appsync&action=AjaxAddPermission',
            type    : 'POST',
            data    : inputData,
            success: function(data)
            {
                this.setState({update: "success"});
            }.bind(this),
            error: function(xhr, status, err)
            {
                this.setState({update: "failed"})
            }.bind(this)
        });
    },
    // Retrieves the umbrellas from the server via an ajax call.
    getUmbrellas: function()
    {
        $.ajax({
            url         : 'index.php?module=appsync&action=AjaxGetAllUmbrellas',
            type        : 'GET',
            dataType    : 'json',
            success: function(data)
            {
                this.setState({
                                    umbrellaList: data
                });
            }.bind(this),
            error: function(xhr, status, err)
            {
                alert(err.toString())
                console.error(this.props.url, status, err.toString());
            }.bind(this)
        });
    },
    render: function()
    {
        // Add some style to push the component down some
        var addStyle = {marginTop: '20px'};
        var button;
        var status;

        // If the username is not empty and the umbrella is set add a button to save the permission
        if(this.state.username != "" && this.state.umbrella != -1)
        {
            button = (
                <div className="form-group">
                    <a onClick={this.add} className="btn btn-md btn-success pull-right">Add</a>
                </div>
            )
        }
        else
        {
            button = (
                <div></div>
            );
        }

        // Display an alert if the update happened successfully or not
        if(this.state.update == "success")
        {
            status = (
                <div style={addStyle} className="alert alert-success" role="alert">
                    <i className="fa fa-check fa-2x"></i> <span>Successfully added permission for {this.state.username}</span>
                </div>
            );
        }
        else if (this.state.update == "failed")
        {
            status = (
                <div style={addStyle} className="alert alert-danger" role="alert">
                    <i className="fa fa-times fa-2x"></i> <span>Failed to add {this.state.username}</span>
                </div>
            );
        }
        else
        {
            status = (
                <div></div>
            );
        }

        return(
            <div>
                {status}
                <div className="col-md-4" style={addStyle}>
                    <h4>Add Permissions</h4>
                    <div className="form-group">
                        <label for="usernameInput">Username</label>
                        <input type="username" className="form-control" id="usernameInput" placeholder="Username"
                            onChange={this.inputChange} ref="usernameInput"></input>
                    </div>
                    <div className="form-group">
                        <label>Umbrella</label>
                        <UmbrellaPickBox umbrellaList={this.state.umbrellaList} change={this.setUmbrella} />
                    </div>
                    {button}
                </div>
            </div>
        );
    }
});

var PermissionRemoveBox = React.createClass({
    getInitialState: function()
    {
        return ({
                    username        : "",
                    userPermissions : null,
                    umbrella        : -1,
                    update          : ""
        });
    },
    inputChange: function()
    {
        var removeUsername = this.refs.usernameInput.value;

        this.setState({
                            username    : removeUsername,
                            umbrella    : -1,
                            update      : ""
        });

        this.getUmbrellaPermissions(removeUsername);
    },
    setUmbrella: function(umbrellaId)
    {
        this.setState({
                            umbrella    : umbrellaId
        });
    },
    remove: function()
    {
        if(confirm("This will remove "+this.state.username+"'s permission to view and alter this umbrella...\n\nAre you sure?"))
        {
            this.removePermission();
        }
    },
    getUmbrellaPermissions: function(username)
    {
        var inputData = {username: username};
        $.ajax({
            url         : 'index.php?module=appsync&action=AjaxGetUmbrellaPermissions',
            type        : 'GET',
            dataType    : 'json',
            data        : inputData,
            success: function(data)
            {
                this.setState({userPermissions: data});
            }.bind(this),
            error: function(xhr, status, err)
            {
                //TODO create error handler
            }.bind(this)
        });
    },
    removePermission: function()
    {
        var inputUmbrella = this.state.umbrella;
        var inputUsername = this.state.username;
        var inputData     = {
                                username    : inputUsername,
                                umbrella    : inputUmbrella
        };

        $.ajax({
            url         : 'index.php?module=appsync&action=AjaxRemovePermission',
            type        : 'POST',
            dataType    : 'json',
            data        : inputData,
            success: function(data)
            {
                this.setState({
                                    update  : "success"
                });
            }.bind(this),
            error: function(xhr, status, err)
            {
                this.setState({
                                    update  : "failed"
                });
            }.bind(this)
        });
    },
    render: function()
    {
        // Add style to make some space above this component
        var removeStyle = {marginTop: '20px'};
        // Create variables for the parts to be displayed
        var umbrellaSelect;
        var button;
        var status;

        // If the userPermissions are set and the userPermissions array is not empty then display the UmbrellaPickBox
        if(this.state.userPermissions != null && this.state.userPermissions.length != 0)
        {
            umbrellaSelect = (
                <div className="form-group">
                    <label>Umbrella</label>
                    <UmbrellaPickBox umbrellaList={this.state.userPermissions}
                                     change={this.setUmbrella} />
                </div>
            );
        }
        else
        {
            umbrellaSelect = (
                <div></div>
            );
        }

        // If the umbrella is set then add a remove button
        if(this.state.umbrella != -1)
        {
            button = (
                <div className="form-group">
                    <a onClick={this.remove} className="btn btn-md btn-danger pull-right">Remove</a>
                </div>
            );
        }
        else
        {
            button = (
                <div></div>
            );
        }

        // Display an alert upon the success or failure of the removal of a permission
        if(this.state.update == "success")
        {
            status = (
                <div style={removeStyle} className="alert alert-success" role="alert">
                    <i className="fa fa-check fa-2x"></i> <span>Successfully removed permission for {this.state.username}</span>
                </div>
            );
        }
        else if (this.state.update == "failed")
        {
            status = (
                <div style={removeStyle} className="alert alert-danger" role="alert">
                    <i className="fa fa-times fa-2x"></i> <span>Failed to remove permission for {this.state.username}</span>
                </div>
            );
        }
        else
        {
            status = (
                <div></div>
            );
        }

        return(
            <div>
                {status}
                <div className="col-md-4" style={removeStyle}>
                    <h4>Remove Permissions</h4>
                    <div className="form-group">
                        <label>Username</label>
                        <input onChange={this.inputChange} type="username" className="form-control" id="usernameInput"
                            placeholder="Username" ref="usernameInput"></input>
                    </div>
                    {umbrellaSelect}
                    {button}
                </div>
            </div>
        );
    }
});
