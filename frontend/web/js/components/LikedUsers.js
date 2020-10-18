class LikedUsers extends React.Component {

    constructor(props) {
        super(props);
        this.closeLikedPopup = this.closeLikedPopup.bind(this);
    }

    closeLikedPopup(event = undefined) {
        event && event.preventDefault();
        this.props.closeLikedPopup(false);
    }

    render() {
        console.log(this.props);
        const likedUsers = this.props.likedUsers;
        return React.createElement(
            "div",
            { className: "liked-users__container", onMouseLeave: this.closeLikedPopup },
            React.createElement(
                "div",
                { className: "liked-users__header" },
                likedUsers.length === 0 ? 'Никто не лайкнул' : 'Понравилось: ' + likedUsers.length
            ),
            React.createElement(
                "a",
                { href: "",
                    onClick: event => this.closeLikedPopup(event),
                    className: "liked-users__close",
                    title: "\u0417\u0430\u043A\u0440\u044B\u0442\u044C \u043E\u043A\u043D\u043E" },
                React.createElement(
                    "i",
                    { className: "material-icons" },
                    "close"
                )
            ),
            React.createElement(
                "div",
                { className: "liked-users__cards" },
                likedUsers.length > 0 && likedUsers.map(user => {
                    return React.createElement(
                        "div",
                        { className: "liked-users__card", key: user.id },
                        React.createElement(
                            "a",
                            { className: "liked-users__avatar circle", href: `/profile/${user.id}`,
                                title: user.username },
                            React.createElement("img", { src: user.picture, alt: "\u0437\u0434\u0435\u0441\u044C \u0431\u044B\u043B\u0430 \u0430\u0432\u0430\u0442\u0430\u0440\u043A\u0430.." })
                        )
                    );
                })
            )
        );
    }
}

export default LikedUsers;