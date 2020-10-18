import Comments from "./Comment.js";
import LikedUsers from "./LikedUsers.js";

export default class PostCard extends React.Component {
    constructor(props) {
        super(props);
        this.likePost = this.likePost.bind(this);
        this.deletePost = this.deletePost.bind(this);
        this.changeLikedPopup = this.changeLikedPopup.bind(this);
        this.showComments = this.showComments.bind(this);
        this.state = {
            showComments: false,
            showLikedUsers: false
        };
    }

    likePost(e) {
        e.preventDefault();
        fetch('/chareit/post/like-post', {
            method: 'POST',
            headers: {
                'content-type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 'postId': this.props.post.id })

        }).then(res => res.json()).then(result => {
            if (result.status === 'success') {
                this.props.likePost(this.props.post.id, result.likedUsers);
                this.setState({
                    showLikedUsers: true
                });
            }
        });
    }

    deletePost(event) {
        event.preventDefault();
        fetch('/chareit/post/delete-post', {
            method: 'POST',
            headers: {
                'content-type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 'postId': this.props.post.id })
        }).then(res => res.json()).then(result => {
            if (result.status === 'success') {
                M.toast({ html: 'Пост удален!' });
                this.props.deletePost(this.props.post.id);
            }
        });
    }

    changeLikedPopup(status) {
        status !== this.state.showLikedUsers && this.setState({
            showLikedUsers: status
        });
    }

    showComments(e) {
        e.preventDefault();
        this.setState({
            showComments: true
        });
    }

    render() {
        let post = this.props.post;
        return React.createElement(
            "div",
            { className: "card post-card", onMouseLeave: () => this.changeLikedPopup(false), onTouchMove: () => this.changeLikedPopup(false) },
            React.createElement(
                "div",
                { className: "card-header" },
                React.createElement(
                    "div",
                    { className: "card-header__userinfo" },
                    React.createElement(
                        "div",
                        { className: "card-header__avatar circle" },
                        React.createElement(
                            "a",
                            { className: "", href: `/profile/${post.user_id}` },
                            React.createElement("img", { className: "", src: `/profile_avatars/thumbnails/${post.picture}`, alt: "" })
                        )
                    ),
                    React.createElement(
                        "div",
                        null,
                        React.createElement(
                            "a",
                            { className: "card-header__username", href: `/profile/${post.user_id}` },
                            post.username
                        ),
                        React.createElement(
                            "div",
                            {
                                className: "card-header__created_at" },
                            post.date
                        )
                    )
                ),
                React.createElement(
                    "div",
                    null,
                    post.isOwner && React.createElement(
                        "a",
                        {
                            href: "",
                            onClick: this.deletePost,
                            title: "\u0423\u0434\u0430\u043B\u0438\u0442\u044C \u043F\u043E\u0441\u0442" },
                        React.createElement(
                            "i",
                            { className: "material-icons" },
                            "clear"
                        )
                    )
                )
            ),
            React.createElement(
                "div",
                { className: "card-image" },
                React.createElement("img", { src: `/uploads/thumbnails/${post.filename}`,
                    alt: "\u0437\u0434\u0435\u0441\u044C \u0431\u044B\u043B \u043F\u043E\u0441\u0442..." })
            ),
            React.createElement(
                "div",
                { className: "card-content" },
                React.createElement(
                    "p",
                    null,
                    post.description
                )
            ),
            React.createElement(
                "div",
                { className: "card-action" },
                React.createElement(
                    "div",
                    null,
                    React.createElement(
                        "a",
                        { href: "",
                            onClick: this.likePost,
                            onMouseEnter: () => this.changeLikedPopup(true),
                            title: "\u041F\u043E\u0441\u0442\u0430\u0432\u0438\u0442\u044C \u043B\u0430\u0439\u043A" },
                        React.createElement(
                            "i",
                            { className: "material-icons" },
                            post.isLikedByUser ? 'favorite' : 'favorite_border'
                        ),
                        React.createElement(
                            "span",
                            { className: "likes-counter" },
                            post.likedUsers.length
                        )
                    ),
                    this.state.showLikedUsers && React.createElement(LikedUsers, { likedUsers: post.likedUsers, closeLikedPopup: this.changeLikedPopup })
                ),
                React.createElement(
                    "div",
                    null,
                    React.createElement(
                        "a",
                        { href: post.filename, download: true, title: "\u0441\u043A\u0430\u0447\u0430\u0442\u044C \u043E\u0440\u0438\u0433\u0438\u043D\u0430\u043B" },
                        React.createElement(
                            "i",
                            { className: "material-icons" },
                            "file_download"
                        )
                    )
                )
            )
        );
    }
}