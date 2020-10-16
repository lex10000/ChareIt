class LikedUsers extends React.Component {

    constructor(props) {
        super(props);
    }

    componentDidMount() {
        fetch('/chareit/default/get-liked-users', {
            'method': 'POST',
            'headers': {'X-CSRF-Token': this.props.csrfToken, 'Content-Type': 'application/x-www-form-urlencoded'},
            'body': 'postId=' + this.props.postId
        })
            .then(Response => Response.json())
            .then((data) => {
            });
    }

    render() {
        return (
            <div>
                    <div className="liked-users__container">
                        <div className="liked-users__header" />
                        <div className="liked-users__close"><i className="material-icons">close</i></div>
                        <div className="liked-users__cards" />
                    </div>
            </div>
        );
    }
}

export default LikedUsers;