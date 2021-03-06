<?php
include_once(__DIR__ . "/Db.php");
include_once(__DIR__ . "/Buddy.php");

class Forum extends Buddy
{
    private $postID;
    private $postTxt;
    private $commentID;
    private $commentTxt;
    private $mod;
    private $limit;

    /**
     * Get the value of postID
     */
    public function getPostID()
    {
        return $this->postID;
    }

    /**
     * Set the value of postID
     *
     * @return  self
     */
    public function setPostID($postID)
    {
        $this->postID = $postID;

        return $this;
    }

    /**
     * Get the value of commentID
     */
    public function getCommentID()
    {
        return $this->commentID;
    }

    /**
     * Set the value of commentID
     *
     * @return  self
     */
    public function setCommentID($commentID)
    {
        $this->commentID = $commentID;

        return $this;
    }

    /**
     * Get the value of commentTxt
     */
    public function getCommentTxt()
    {
        return $this->commentTxt;
    }

    /**
     * Set the value of commentTxt
     *
     * @return  self
     */
    public function setCommentTxt($commentTxt)
    {
        $this->commentTxt = $commentTxt;

        return $this;
    }

    /**
     * Get the value of postTxt
     */
    public function getPostTxt()
    {
        return $this->postTxt;
    }

    /**
     * Set the value of postTxt
     *
     * @return  self
     */
    public function setPostTxt($postTxt)
    {
        $this->postTxt = $postTxt;

        return $this;
    }

    // fetch alle forum posts en de bijhorende user
    public function fetchPosts()
    {
        $conn = Db::getConnection();
        $stmt = $conn->prepare('SELECT * FROM post INNER JOIN user ON post.userID = user.userID ORDER BY post.postID DESC');
        $stmt->execute();
        $content = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $content;
    }

    // verstuur comment
    public function sendComment($postID, $userID, $commentTxt)
    {
        $conn = Db::getConnection();
        $stmt = $conn->prepare('INSERT INTO comments (userID, postID, commentsTxt) VALUES (:userID, :postID, :commentsTxt)');
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':postID', $postID);
        $stmt->bindParam(':commentsTxt', $commentTxt);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function fetchComments($postID)
    {
        $conn = Db::getConnection();
        $stmt = $conn->prepare('SELECT * FROM comments INNER JOIN user on comments.userID = user.userID WHERE comments.postID = :postID');
        $stmt->bindParam(':postID', $postID);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function specificPost($postID)
    {
        $conn = Db::getConnection();
        $stmt = $conn->prepare('SELECT * FROM post INNER JOIN user ON post.userID = user.userID WHERE post.postID = :postID');
        $stmt->bindParam(':postID', $postID);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public function specificComments($postID)
    {
        $conn = Db::getConnection();
        $stmt = $conn->prepare('SELECT * FROM comments INNER JOIN user on comments.userID = user.userID WHERE comments.postID = :postID');
        $stmt->bindParam(':postID', $postID);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * Get the value of mod
     */
    public function getMod()
    {
        return $this->mod;
    }

    /**
     * Set the value of mod
     *
     * @return  self
     */
    public function setMod($mod)
    {
        $this->mod = $mod;

        return $this;
    }

    // check if user is moderator
    public function checkMod($userID)
    {
        $conn = Db::getConnection();
        $stmt = $conn->prepare('SELECT modStatus FROM modteam WHERE userID = :userID');
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    // create new post
    public function newPost($userID, $postTxt)
    {
        $conn = Db::getConnection();
        $stmt = $conn->prepare('INSERT INTO post (userID, postTxt) VALUES (:userID, :postTxt)');
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':postTxt', $postTxt);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    // check if post is pinned
    public function checkPinned()
    {
        $conn = Db::getConnection();
        $stmt = $conn->prepare('SELECT * FROM post');
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    // pin a post
    public function pinPost($postID)
    {
        $conn = Db::getConnection();
        $stmt = $conn->prepare('UPDATE post SET pin = 1 WHERE postID = :postID');
        $stmt->bindParam(':postID', $postID);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public function isPinned($postID)
    {
        $conn = Db::getConnection();
        $stmt = $conn->prepare('SELECT pin FROM post WHERE postID = :postID');
        $stmt->bindValue(':postID', $postID);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result['pin'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function unpinPost($postID)
    {
        $conn = Db::getConnection();
        $stmt = $conn->prepare('UPDATE post SET pin = 0 WHERE postID = :postID');
        $stmt->bindValue(':postID', $postID);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * Get the value of limit
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the value of limit
     *
     * @return  self
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function saveLike()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare('INSERT into comments_like (userID, commentID) values (:userID, :commentID)');
        $userID = $this->userID;
        $commentID = $this->commentID;
        $statement->bindValue(":userID", $userID);
        $statement->bindValue(":commentID", $commentID);
        $result = $statement->execute();
        return $result;
    }

    public function getAllLikes()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare('SELECT COUNT(comment_likeID) FROM comments_like WHERE commentID = :commentID');
        $commentID = $this->commentID;
        $statement->bindValue(":commentID", $commentID);
        $result = $statement->execute();
        return $result;
    }

    public function checkLike()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare('SELECT COUNT(comment_likeID) FROM comments_like WHERE userID = :userID AND commentID = :commentID');
        $commentID = $this->commentID;
        $userID = $this->userID;
        $statement->bindValue(":commentID", $commentID);
        $statement->bindValue(":userID", $userID);
        $result = $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        $liked = implode(" ", $result);
        return $liked;
    }

    // get comment with most likes from specific post and retrieve user data as well
    public function mostLikes($postID)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare('SELECT * FROM comments_like INNER JOIN comments ON comments_like.commentID = comments.commentID INNER JOIN user ON comments.userID = user.userID WHERE comments.postID = :postID GROUP BY comments_like.commentID ORDER BY COUNT(comments_like.userID) DESC LIMIT 1');
        $statement->bindValue(':postID', $postID);
        $result = $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    // check if there's at least one liked comment in a topic
    public function checkForLikes($postID)
    {
        $conn = Db::getConnection();
        $stmt = $conn->prepare('SELECT * FROM comments_like INNER JOIN comments ON comments_like.commentID = comments.commentID WHERE postID = :postID');
        $stmt->bindValue(':postID', $postID);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }

        return $result;
    }
}
