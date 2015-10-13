var React = require('react');

var MembersTable = React.createClass({
  render: function() {
    return (
      <div>
        I am rendered with React. I have a property: {this.props.someAttribute}
      </div>
    );
  }
});

module.exports = MembersTable;
