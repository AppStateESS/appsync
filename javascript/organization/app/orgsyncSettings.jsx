import React from 'react-dom/node_modules/react';
import ReactDOM from 'react-dom';

export default class OrgsyncSettingsBox extends React.Component {
  render() {
    return (<OrgsyncSettingsViewBox umbrellaList={this.props.umbrellaList}
                                    setLiveState={this.props.setLiveState}/>);
  }
}

// Component responsible for controlling the other compenents involved in
// configuring settings for the portal.  Also responsible for communication
// with the server.
var OrgsyncSettingsViewBox = React.createClass({
    // Sets up an initial state for the class, with default values.
    getInitialState: function()
    {
        return {
                    toggleState : "",
                    liveUrl     : "Live URL",
                    testUrl     : "Test URL",
                    orgKey      : "OrgSync Key",
                    bannerUrl   : "Banner URL",
                    alert       : undefined
        };
    },
    // When the component mounts retrieve the current settings.
    componentWillMount: function()
    {
        this.retrieveSettings();
    },
    // Set the toggleState to the value passed and reset the alert.
    changeToggleState: function(value)
    {
        this.setState({
                            toggleState : value,
                            alert       : undefined
        });
    },
    // Pass the given values and the current toggleState to the settings and reset the alert.
    saveChanges: function(liveUrlInput, testUrlInput, keyInput, bannerUrlInput)
    {
        this.setState({
                            alert   : undefined
        });

        this.setSettings(this.state.toggleState,
                         liveUrlInput,
                         testUrlInput,
                         keyInput,
                         bannerUrlInput
        );
    },
    // Retrieve the current settings via AJAX request.
    retrieveSettings: function()
    {
        $.ajax({
            url         : 'index.php?module=appsync&action=AjaxGetSettings',
            type        : 'GET',
            dataType    : 'json',
            success: function(data){
                this.setState({
                                    toggleState : data.state,
                                    liveUrl     : data.liveUrl,
                                    testUrl     : data.testUrl,
                                    orgKey      : data.key,
                                    bannerUrl   : data.bannerUrl
                });
                this.props.setLiveState(data.state);
            }.bind(this),
            error: function(){

            }.bind(this)
        });
    },
    // Set the current settings via AJAX POST request.
    setSettings: function(stateValue, liveUrlInput, testUrlInput, keyInput, bannerUrlInput)
    {
        var inputData = {
                            state       : stateValue,
                            liveUrl     : liveUrlInput,
                            testUrl     : testUrlInput,
                            key         : keyInput,
                            bannerUrl   : bannerUrlInput
        };

        $.ajax({
            url         : 'index.php?module=appsync&action=AjaxSetSettings',
            type        : 'POST',
            dataType    : 'json',
            data        : inputData,
            success: function(data)
            {
                this.setState({
                                    alert   : data
                });
                this.retrieveSettings();
            }.bind(this),
            error: function(){

            }.bind(this)
        });
    },
    // Render function
    render: function()
    {
        return (
                <div>
                    <AlertBox alert={this.state.alert}/>
                    <LiveToggleBox state={this.state.toggleState}
                                   toggle={this.changeToggleState}/>
                    <SettingsInputBox liveUrl={this.state.liveUrl}
                                      testUrl={this.state.testUrl}
                                      orgKey={this.state.orgKey}
                                      bannerUrl={this.state.bannerUrl}
                                      save={this.saveChanges}/>
                </div>
        );
    }
});


// Component responsible for handling the creation and actions of the toggle.
var LiveToggleBox = React.createClass({
    // Calls the parent class' toggle function with the LIVE flag
    live: function()
    {
        this.props.toggle("LIVE");
    },
    // Calls the parent class' toggle function with the TEST flag
    test: function()
    {
        this.props.toggle("TEST");
    },
    // Render Function
    render: function()
    {
        // Set the live and test variables to false initially
        var live = false;
        var test = false;

        // Set the appropriate live/test variable to true
        if(this.props.state == "LIVE")
        {
            live = true;
        }
        else if(this.props.state == "TEST")
        {
            test = true;
        }

        // Set the classes of the live button based on the live variable
        var liveClasses = classNames({
            'btn'           : true,
            'btn-default'   : true,
            'active'        : live
        });

        // Set the classes of the test button based on the test variable
        var testClasses = classNames({
            'btn'           : true,
            'btn-default'   : true,
            'active'        : test
        });

        // Set the style of the class to create space above it
        var toggleStyle = {marginTop: '25px'};

        return(
            <div style={toggleStyle}>
                <h4>Currently using {this.props.state}</h4>
                <div className="col-md-12">
                        <div className="btn-group col-md-6">
                            <label onClick={this.live} className={liveClasses}>
                                Use Live Server
                            </label>
                            <label onClick={this.test} className={testClasses}>
                                Use Test Server
                            </label>
                        </div>
                </div>
            </div>
        );
    }
});


// Component responsible for the inputs and the save button.
var SettingsInputBox = React.createClass({
    // On click function that passes up the current values of the inputs to the parent component.
    save: function()
    {
        this.props.save(this.refs.liveUrlInput.value,
                        this.refs.testUrlInput.value,
                        this.refs.keyInput.value,
                        this.refs.bannerUrlInput.value
        );
    },
    // Render function
    render: function()
    {
        // Set the style of the class to create space above it
        var inputsStyle ={marginTop: '20px'};

        return(
            <div style={inputsStyle} className="col-md-5">
                <div className="form-group">
                    <label>Live URL</label>
                    <input className="form-control" placeholder={this.props.liveUrl} ref="liveUrlInput"></input>
                </div>
                <div className="form-group">
                    <label>Test URL</label>
                    <input className="form-control" placeholder={this.props.testUrl} ref="testUrlInput"></input>
                </div>
                <div className="form-group">
                    <label>Key</label>
                    <input className="form-control" placeholder={this.props.orgKey} ref="keyInput"></input>
                </div>
                <div className="form-group">
                    <label>Banner URL</label>
                    <input className="form-control" placeholder={this.props.bannerUrl} ref="bannerUrlInput"></input>
                </div>
                <button onClick={this.save} className="btn btn-success">
                    <i className="fa fa-save"></i> Save
                </button>
            </div>
        );
    }
});

// Component responsible for the alert class that lets the admin know when something has
// saved successfully or not.
var AlertBox = React.createClass({
    // Render function
    render: function()
    {
        // If the alert is undefined or the message is an empty string then
        // just display an empty div
        if(this.props.alert == undefined || this.props.alert.message == "")
        {
            return (
                <div></div>
            );
        }
        else
        {
            // Set the alert class variables up as false initially
            var alert;
            var success = false;
            var error   = false;

            // set the class variables
            if(this.props.alert.type == "success")
            {
                var success = true;
            }
            else
            {
                var error = true;
            }

            // Set the alert class via classNames
            var alertClass = classNames({
                'alert'         : true,
                'alert-success' : success,
                'alert-danger'  : error
            });

            // Set the appropriate class for the font awesome icon
            var faClass = classNames({
                'fa'        : true,
                'fa-2x'     : true,
                'fa-check'  : success,
                'fa-times'  : error
            });

            // Create the font awesome icon
            var alertSymbol = (
                <i className={faClass}></i>
            );

            return (
                <div className={alertClass} role="alert">
                    {alertSymbol} {this.props.alert.message}
                </div>
            );
        }
    }
});
