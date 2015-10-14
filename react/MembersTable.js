var React = require('react');
var FixedDataTable = require('fixed-data-table');

var Table = FixedDataTable.Table;
var Column = FixedDataTable.Column;

class MembersTable extends React.Component {
  render() {
    return (
      <div>
        I am rendered with React. I have a property: {this.props.members}
      </div>
    );
  }
}

MembersTable.propTypes = {
  members: React.PropTypes.array,
};

module.exports = MembersTable;
