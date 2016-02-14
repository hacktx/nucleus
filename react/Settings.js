var React = require('react');

class Settings extends React.Component {
  constructor(props) {
    super(props);

    this.onChange = this.onChange.bind(this);
  }

  render() {
    return (
      <div className="col-md-6 col-md-offset-3">
        <form className="form" action="/settings" method="post">
          <div className="panel panel-default">
            <div className="panel-body">
                <div className="form-group">
                  <div className="checkbox">
                    <label>
                      <input type="checkbox" name="applications_disabled" checked={!this.props.settings.applications_open} onChange={this.onChange}/> Disable Applications
                    </label>
                  </div>
                </div>
            </div>
          </div>
          <button type="submit" className="btn btn-primary pull-right">Save</button>
        </form>
      </div>
    );
  }

  onChange() {
    this.setState({isChecked: !this.state.isChecked});
  }
}

Settings.propTypes = {
  members: React.PropTypes.array,
};

module.exports = Settings;
