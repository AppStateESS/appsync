import React from 'react-dom/node_modules/react';
import ReactDOM from 'react-dom';

export default class UmbrellaPickBox extends React.Component {
  render() {
    return (<UmbrellaBox umbrellaList={this.props.umbrellaList}
                         change={this.props.change}/>);
  }
}

var UmbrellaBox = React.createClass({
    // Retrieves the value of the dropdown and passes it to the parent component.
    change: function()
    {
        var uChoice = ReactDOM.findDOMNode(this.refs.umbrellaChoice);
        var value   = uChoice.value;

        this.props.change(value);
    },
    // Render function
    render: function()
    {
        // Set up the options Array and pull the data from the props
        var options = Array();
        var data    = this.props.umbrellaList;

        var defaultOption = {
                                umbrella_id     : -1,
                                umbrella_name   : "Pick an umbrella..."
        };

        // Push the defaultOption onto the array
        options.push(defaultOption);

        // Loop through pushing all the data items onto the array
        var i  = 0;
        for(i; i < data.length; i++)
        {
            options.push(data[i]);
        }

        // Create all the options for the dropdown, using the options from the array
        var selectOptions = options.map(function(node)
        {
            return(
                <option key={node.umbrella_id} value={node.umbrella_id}>{node.umbrella_name}</option>
            );
        });

        return(
            <div>
                <select onChange={this.change} className="form-control" ref="umbrellaChoice">
                    {selectOptions}
                </select>
            </div>
        );
    }
});
