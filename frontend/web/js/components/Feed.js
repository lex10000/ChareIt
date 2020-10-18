import PostCard from "./PostCard.js";

class Feed extends React.Component {

    constructor(props) {
        super(props);
        this.getPosts = this.getPosts.bind(this);
        this.deletePost = this.deletePost.bind(this);
        this.likePost = this.likePost.bind(this);
        this.handleScroll = this.handleScroll.bind(this);
        this.state = {
            posts: [],
            isLoaded: false
        };
    }

    getPosts(startPage = undefined) {
        const url = startPage ? `/get-${this.props.feedType}/${startPage}` : `/get-${this.props.feedType}`;
        fetch(url).then(res => res.json()).then(result => {
            if (result.status === 'success') {
                let posts = this.state.posts;
                posts.push(...result.posts);
                window.addEventListener('scroll', this.handleScroll);
                this.setState({
                    posts: posts,
                    isLoaded: true
                });
            } else {
                this.setState({
                    isLoaded: true
                });
            }
        });
    }

    deletePost(id) {
        let posts = this.state.posts;
        const index = posts.findIndex(post => {
            return post.id === id;
        });
        posts.splice(index, 1);
        this.setState({ posts });
    }

    likePost(id, likedUsers) {
        let posts = this.state.posts;
        let post = posts.find(item => item.id === id);
        let i = posts.findIndex(item => item.id === id);
        post.isLikedByUser = !post.isLikedByUser;
        post.likedUsers = likedUsers;
        posts[i] = post;
        this.setState({
            posts: posts
        });
    }

    componentDidMount() {
        this.getPosts();
    }

    handleScroll() {
        if (this.props.feedType === 'top') {
            return null;
        } else {
            if (this.refs.feedRef.scrollHeight - window.pageYOffset < 1500) {
                window.removeEventListener('scroll', this.handleScroll);
                this.getPosts(this.state.posts.length);
            }
        }
    }

    render() {
        if (this.state.isLoaded) {
            if (this.state.posts.length === 0) return React.createElement(
                'div',
                null,
                '\u041F\u043E\u043A\u0430 \u043F\u043E\u0441\u0442\u043E\u0432 \u043D\u0435\u0442...'
            );
            return React.createElement(
                'div',
                { ref: 'feedRef' },
                this.state.posts.map(post => React.createElement(PostCard, {
                    key: post.id,
                    post: post,
                    deletePost: this.deletePost,
                    likePost: this.likePost
                }))
            );
        } else {
            return React.createElement(
                'div',
                { className: 'progress' },
                React.createElement('div', { className: 'indeterminate yellow' })
            );
        }
    }
}

export default Feed;