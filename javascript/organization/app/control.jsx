import React from 'react-dom/node_modules/react';
import ReactDOM from 'react-dom';

// This is here to make webpack work, if you can find a better way please fix.
export default class ControlBox extends React.Component {
  render() {
    return <ControlActionBox add={this.props.add}
                             remove={this.props.remove}
                             state={this.props.state}
                             inputData={this.props.inputData}
                             outputData={this.props.outputData}
                             portalMembers={this.props.portalMembers}
                             errorData={this.props.errorData}
                             singleRemove={this.props.singleRemove}
                             groupMembers={this.props.groupMembers}
                             groupId={this.props.groupId} />;
  }
}

// The main component for managing which component to display based on the parent state
var ControlActionBox = React.createClass({
    // On click function for passing the values of the textbox for AddBox and RemoveBox up
    // to the parent class.
    click: function(text)
    {
        if(this.props.state == "ADD")
        {
            this.props.add(text);
        }
        else if(this.props.state == "REMOVE")
        {
            this.props.remove(text);
        }
    },
    // Passes the email to the parent class' singleRemove function
    singleRemove: function(email)
    {
        this.props.singleRemove(email);
    },
    // Render Function
    render: function()
    {
        // If the state is set to 'LIST' then display the ListBox component
        if(this.props.state == "LIST")
        {
            return(
                <div>
                    <ListBox portalMembers={this.props.portalMembers}
                             groupMembers={this.props.groupMembers}
                             singleRemove={this.singleRemove}
                             groupId={this.props.groupId} />
                </div>
            );
        }
        // If the state is set to 'ADD' then display the AddBox component
        else if(this.props.state == "ADD")
        {
            return(
                <div>
                    <AddBox click={this.click}
                            errorData={this.props.errorData} />
                </div>
            );
        }
        // If the state is set to 'REMOVE' then display the RemoveBox component
        else if(this.props.state == "REMOVE")
        {
            return(
                <div>
                    <RemoveBox click={this.click}
                               errorData={this.props.errorData} />
                </div>
            );
        }
        // If the state is set to either 'PROCESSING' then display the ActionBox component
        else if(this.props.state == "PROCESSING")
        {
            return(
                <div>
                    <ActionBox inputData={this.props.inputData}
                               outputData={this.props.outputData}
                               state={this.props.state} />
                </div>
            );
        }
        // Fall through which "should not" happen but is here just in case
        else
        {
                return(
                    <div></div>
                );
        }
    }
});

var ListBox = React.createClass({
    // Function passes the value of the email up to the parent class' function for removing
    // a single student by email.
    remove: function(email)
    {
        this.props.singleRemove(email);
    },
    // Render Function
    render: function()
    {
        // Set up the style variables
        var controlStyle  = {marginTop: '25px'};
        var noMemberStyle = {marginTop: '10px'};
        // Pre initialize the members array
        var members       = Array();

        // If the group is set use the group's members, if not set use the portal members,
        if(this.props.groupId == -1)
        {
            members = this.props.portalMembers;
        }
        else
        {
            members = this.props.groupMembers;
        }

        // If the members array is empty just return a line about there being no members present
        if(members == false || members.length == 0)
        {
            return (
                <div style={noMemberStyle}>
                    <p>There are no members at present, to add some members click on the Add Members tab.</p>
                </div>
            );
        }

        // The function callback needs to be put into a local variable because of scope
        var listRemove = this.remove;

        // Map each member node into a table row
        var memberRows = members.map(function(node){
            return (
                <tr key={node.email}>
                    <td>
                        {node.first_name} {node.last_name}
                    </td>
                    <td>
                        {node.email}
                    </td>
                    <td>
                        <OptionBox listRemove={listRemove}
                                   email={node.email}/>
                    </td>
                </tr>);
        });

        return(
                <div className="row">
                    <div className="col-md-6">
                        <table style={controlStyle} className="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {memberRows}
                            </tbody>
                        </table>
                    </div>
                </div>
        );
    }
});

var AddBox = React.createClass({
    // On click function that retrieves the value of the textbox and passes it up to
    // be parsed and the students added.
    click: function()
    {
        // Retrieve the value of the input via its reference
        var addText = this.refs.addText.value;

        this.props.click(addText);
    },
    // Render Function
    render: function()
    {
        // Set up the style variables
        var controlStyle = {marginTop: '25px'};
        var buttonStyle  = {marginTop: '15px'};
        // Start the error variable at false
        var addError     = false;

        // Only set the addError to true if the location of the errorData is 'Add'
        if(this.props.errorData.location == "Add")
        {
            addError = true;
        }

        // Set up the classes for the div using classnames
        var addInputClasses = classNames({
            'col-md-4' : true,
            'has-error': addError
        });

        return(
            <div>
                <div className="row">
                    <div className={addInputClasses}>
                        <textarea style={controlStyle} className="form-control"
                            rows="5" cols="40" ref="addText"></textarea>
                    </div>
                </div>
                <a onClick={this.click} style={buttonStyle} className="btn btn-lg btn-success">Add</a>
            </div>
        );
    }
});

var RemoveBox = React.createClass({
    // On click function that retrieves the value of the textbox and passes it up to
    // be parsed and the students removed.
    click: function()
    {
        var removeText = this.refs.removeText.value;

        this.props.click(removeText);
    },
    // Render Function
    render: function()
    {
        // Set up the style variables
        var controlStyle = {marginTop: '25px'};
        var buttonStyle  = {marginTop: '15px'};
        // Start the error variable at false
        var removeError  = false;

        // Only set the removeError to true if the location of the errorData is 'Remove'
        if(this.props.errorData.location == "Remove")
        {
            removeError = true;
        }

        // Set up the classes for the div using classnames
        var removeInputClasses = classNames({
            'col-md-4'  : true,
            'has-error' : removeError
        });

        return(
            <div>
                <div className="row">
                    <div className={removeInputClasses}>
                        <textarea style={controlStyle} className="form-control"
                            rows="5" cols="40" ref="removeText"></textarea>
                    </div>
                </div>
                <a onClick={this.click} style={buttonStyle} className="btn btn-lg btn-success">Remove</a>
            </div>
        );
    }
});

var ActionBox = React.createClass({
    // Render Function
    render: function()
    {
        // // If the inputData array is undefined, then just return an empty div
        if(this.props.inputData == undefined)
        {
            return (
                <div></div>
            );
        }

        // Set up the style variables
        var controlStyle = {marginTop: '25px'};
        // Create an empty array to add the data to
        var data          = Array();
        // Variables for the for loop
        var i             = 0;
        var cmpCnt        = 0;
        var errorOccurred = false;
        var errorCnt      = 0;

        // Loop through the inputData array adding
        for(i; i < this.props.inputData.length; i++)
        {
            var datum   = Array();
            datum.input = this.props.inputData[i].input;

            if(this.props.outputData[i] == undefined)
            {
                datum.status = undefined;
                datum.name   = undefined;
            }
            else
            {
                datum.status = this.props.outputData[i].status;
                datum.name   = this.props.outputData[i].name;
                datum.added  = this.props.outputData[i].added;
                cmpCnt++;

                if(!(datum.status == 1))
                {
                    errorOccurred = true;
                    errorCnt++;
                }
            }

            data.push(datum);
        }
        // Calculate the percentage for the progress bar
        var percentComplete = (cmpCnt / this.props.inputData.length) * 100;

        var table = (<div></div>);
        if(data.length > 50)
        {
            table = <LargeProgressTable data={data}
                                        errorOccurred={errorOccurred} />
        }
        else
        {
            table = <SmallProgressTable data={data}
                                        errorOccurred={errorOccurred} />
        }

        return(
            <div style={controlStyle}>
                <ProgressText completedNum={cmpCnt}
                              total={this.props.inputData.length}
                              errorNum={errorCnt}/>
                <div className="row">
                    <div className="col-md-6">
                        <ProgressBarBox state={this.props.state}
                                        percentComplete={percentComplete}
                                        errorOccurred={errorOccurred}/>
                    </div>
                </div>
                {table}
            </div>
        );
    }
});

var ProgressText = React.createClass({
    render: function()
    {
        var errorCntMsg = (<div></div>);
        if(this.props.errorNum == 1)
        {
            errorCntMsg = (<p><i className="fa fa-times text-danger"></i> {this.props.errorNum} error has occurred</p>);
        }
        else if(this.props.errorNum > 0)
        {
            errorCntMsg = (<p><i className="fa fa-times text-danger"></i> {this.props.errorNum} errors have occurred</p>);
        }
        return (
                <div>
                    <p><i className="fa fa-check text-success"></i> {this.props.completedNum} of {this.props.total} completed</p>
                    {errorCntMsg}
                </div>
        );
    }
});

var SmallProgressTable = React.createClass({
    render: function()
    {
        var data = this.props.data;

        // Create the rows based on the status
        var rows = data.map(function(node){
            if(node.status == 0)
            {
                return(
                    <tr key={node.input}>
                        <td>{node.input}</td>
                        <td><i className="fa fa-times text-danger"></i> Error retrieving student</td>
                        <td><i className="fa fa-times text-danger"></i></td>
                    </tr>
                );
            }
            else if(node.status == 1)
            {
                if(node.added)
                {
                    var added = (<i className="fa fa-plus text-info"></i>)
                }
                return(
                    <tr key={node.input}>
                        <td>{node.input}</td>
                        <td>{node.name}</td>
                        <td><i className="fa fa-check text-success"></i> {added}</td>
                    </tr>
                );
            }
            else if(node.status == 2)
            {
                return(
                    <tr key={node.input}>
                        <td>{node.input}</td>
                        <td>{node.name}</td>
                        <td><i className="fa fa-times text-danger"></i></td>
                    </tr>
                );
            }
            else if(node.name == undefined && node.status == undefined)
            {
                return(
                    <tr key={node.input}>
                        <td>{node.input}</td>
                        <td><i className="fa fa-spinner"></i></td>
                        <td><i className="fa fa-spinner"></i></td>
                    </tr>
                );
            }
        });

        return (
            <div className="row">
                <div className="col-md-6">
                    <table className="table table-striped">
                        <thead>
                            <tr>
                                <th>Input</th>
                                <th>Student Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {rows}
                        </tbody>
                    </table>
                </div>
            </div>
        )

    }
});

var LargeProgressTable = React.createClass({
    render: function()
    {
        if(!this.props.errorOccurred)
        {
            return (<div></div>);
        }

        var data = this.props.data;

        // Create the rows based on the status but only if they failed
        var rows = data.map(function(node){
            if(node.status == 0)
            {
                return(
                    <tr key={node.input}>
                        <td>{node.input}</td>
                        <td><i className="fa fa-times text-danger"></i> Error retrieving student</td>
                        <td><i className="fa fa-times text-danger"></i></td>
                    </tr>
                );
            }
            else if(node.status == 2)
            {
                return(
                    <tr key={node.input}>
                        <td>{node.input}</td>
                        <td>{node.name}</td>
                        <td><i className="fa fa-times text-danger"></i></td>
                    </tr>
                );
            }
        });

        return (
            <div className="row">
                <div className="col-md-6">
                    <table className="table table-striped">
                        <thead>
                            <tr>
                                <th>Input</th>
                                <th>Student Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {rows}
                        </tbody>
                    </table>
                </div>
            </div>
        )
    }
});


var OptionBox = React.createClass({
    // Calls the parent class listRemove function and passes the appropriate email for
    // this student node.
    listRemove: function()
    {
        this.props.listRemove(this.props.email);
    },
    // Render Function
    render: function()
    {
        return(
            <button onClick={this.listRemove} type="button" className="close">
                <span aria-hidden="true">
                    <i className="fa fa-trash"></i>
                </span>
            </button>
        );
    }
});

var ProgressBarBox = React.createClass({
    // Render Function
    render: function()
    {
        // Set the class variables
        var complete         = false;
        var success          = true;
        var danger           = false;
        var active           = true;
        var percentage       = this.props.percentComplete + '%'
        // Set the style variable
        var progressBarStyle = {
                                    width   : percentage
        };

        // If the percentComplete equals 100 percent then set complete to
        // true and active to false
        if(this.props.percentComplete == 100)
        {
            complete = true;
            active   = false;
        }

        // If the errorOccurred prop is set then set the success to false and danger to true
        if(this.props.errorOccurred)
        {
            success = false;
            danger  = true;
        }

        // Set the progress bar classnames
        var progressBarClasses = classNames({
            'progress-bar'          : true,
            'progress-bar-striped'  : !(complete),
            'progress-bar-success'  : success,
            'progress-bar-danger'   : danger,
            'active'                : active
        });

        return(
            <div className="progress">
                <div className={progressBarClasses} role="progressbar" aria-valuenow="40"
                     aria-valuemin="0" aria-valuemax="100" style={progressBarStyle}>
                </div>
            </div>
        )

    }
});
