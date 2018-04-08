<?php

namespace Webklex\IMAP;

use Carbon\Carbon;
use Webklex\IMAP\Folder;

/**
 * IMAP message search query builder.
 * 
 * @see http://php.net/manual/en/function.imap-search.php
 */
class Search
{
    /**
     * Date format.
     *
     * @var string
     */
    public $dateFormat = 'd M y';

    /**
     * IMAP message fetch option: null, FT_UID or FT_PEEK.
     * 
     * - null: uses option set in configuration key "imap.options.fetch"
     * - FT_UID: mark messages as read when fetching them
     * - FT_PEEK: fetch the messages without setting the "read" flag
     *
     * @var null|integer
     */
    protected $fetchOption = null;

    /**
     * MIME character set to use when searching strings.
     *
     * @var string
     */
    public $charset = null;

    /**
     * Fetch the messages body?
     *
     * @var boolean
     */
    public $fetchBody = true;

    /**
     * Fetch the messages attachments?
     *
     * @var boolean
     */
    public $fetchAttachments = true;

    /**
     * Folder to perform the search in.
     *
     * @var \Webklex\IMAP\Folder
     */
    protected $folder;

    /**
     * Search statements.
     *
     * @var string[]
     */
    protected $statements = [];

    /**
     * Start building a new message search query in a folder.
     *
     * @param \Webklex\IMAP\Folder $folder
     */
    public function __construct(Folder $folder)
    {
        $this->folder = $folder;
    }

    /**
     * Get the "where" statements.
     *
     * @return array[]
     */
    public function getStatements()
    {
        return $this->statements;
    }

    /**
     * Add a statement to the query.
     * 
     * Values containing a Carbon instance will be converted to string as per
     * $this->dateFormat.
     *
     * @param string $key
     * @param mixed $value (optional)
     * @return $this
     */
    public function addStatement()
    {
        $args = func_get_args();
        
        // Statement key
        $key = $args[0];
        $statement = [$key];

        // Optional statement value
        if (count($args) == 2) {
            $value = $args[1];
            if ($value instanceof Carbon) {
                $value = $value->format($this->dateFormat);
            }
            $statement = [$key, $value];
        }

        $this->statements[] = $statement;

        return $this;
    }

    /**
     * Set search to return all messages matching the rest of the criteria.
     *
     * @return $this
     */
    public function all()
    {
        return $this->addStatement('ALL');
    }

    /**
     * Match messages with the \\ANSWERED flag set.
     *
     * @return $this
     */
    public function answered()
    {
        return $this->addStatement('ANSWERED');
    }

    /**
     * Match messages that have not been answered.
     *
     * @return $this
     */
    public function unanswered()
    {
        return $this->addStatement('UNANSWERED');
    }

    /**
     * Match messages with $expression in the Bcc: field.
     *
     * @param string $expression
     * @return $this
     */
    public function bcc($expression)
    {
        return $this->addStatement('BCC', $expression);
    }

    /**
     * Match messages with Date: before $date.
     *
     * @param string|\Carbon\Carbon $date
     * @return $this
     */
    public function before($date)
    {
        return $this->addStatement('BEFORE', $date);
    }

    /**
     * Match messages with $expression in the body of the message.
     *
     * @param string $expression
     * @return $this
     */
    public function body($expression)
    {
        return $this->addStatement('BODY', $expression);
    }

    /**
     * Match messages with $expression in the Cc: field.
     *
     * @param string $expression
     * @return $this
     */
    public function cc($expression)
    {
        return $this->addStatement('CC', $expression);
    }

    /**
     * Match deleted messages.
     *
     * @return $this
     */
    public function deleted()
    {
        return $this->addStatement('DELETED');
    }

    /**
     * Match messages that are not deleted.
     *
     * @return $this
     */
    public function undeleted()
    {
        return $this->addStatement('UNDELETED');
    }

    /**
     * Match messages with the \\FLAGGED (sometimes referred to as Important or Urgent) flag set.
     *
     * @return $this
     */
    public function flagged()
    {
        return $this->addStatement('FLAGGED');
    }

    /**
     * Match messages that are not flagged.
     *
     * @return $this
     */
    public function unflagged()
    {
        return $this->addStatement('UNFLAGGED');
    }

    /**
     * Match messages with $expression in the From: field.
     *
     * @param string $expression
     * @return $this
     */
    public function from($expression)
    {
        return $this->addStatement('FROM', $expression);
    }

    /**
     * Match messages with $keyword as a keyword.
     *
     * @param string $keyword
     * @return $this
     */
    public function containsKeyword($keyword)
    {
        return $this->addStatement('KEYWORD', $keyword);
    }

    /**
     * Match messages with $keyword as a keyword.
     *
     * @param string $keyword
     * @return $this
     */
    public function doesNotContainKeyword($keyword)
    {
        return $this->addStatement('UNKEYWORD', $keyword);
    }

    /**
     * Match new messages.
     *
     * @return $this
     */
    public function new()
    {
        return $this->addStatement('NEW');
    }

    /**
     * Match old messages.
     *
     * @return $this
     */
    public function old()
    {
        return $this->addStatement('OLD');
    }

    /**
     * Match messages with Date: matching $date.
     *
     * @param string|\Carbon\Carbon $date
     * @return $this
     */
    public function on($date)
    {
        return $this->addStatement('ON', $date);
    }

    /**
     * Match messages with the \\RECENT flag set.
     *
     * @return $this
     */
    public function recent()
    {
        return $this->addStatement('RECENT');
    }

    /**
     * Match messages that have been read (the \\SEEN flag is set).
     *
     * @return $this
     */
    public function seen()
    {
        return $this->addStatement('SEEN');
    }

    /**
     * Alias for $this->seen() method.
     *
     * @return $this
     */
    public function read()
    {
        return $this->seen();
    }    

    /**
     * Match messages which have not been read yet.
     *
     * @return $this
     */
    public function unseen()
    {
        return $this->addStatement('UNSEEN');
    }

    /**
     * Alias for $this->unseen() method.
     *
     * @return $this
     */
    public function unread()
    {
        return $this->unseen();
    }    

    /**
     * Match messages with Date: after $date.
     *
     * @param string|\Carbon\Carbon $date
     * @return $this
     */
    public function since($date)
    {
        return $this->addStatement('SINCE', $date);
    }

    /**
     * Match messages with $expression in the subject.
     *
     * @param string $expression
     * @return $this
     */
    public function subject($expression)
    {
        return $this->addStatement('SUBJECT', $expression);
    }

    /**
     * Match messages with text $text.
     *
     * @param string $text
     * @return $this
     */
    public function text($text)
    {
        return $this->addStatement('TEXT', $text);
    }

    /**
     * Match messages with $expression in the Bcc: field.
     *
     * @param string $expression
     * @return $this
     */
    public function to($expression)
    {
        return $this->addStatement('TO', $expression);
    }

    /**
     * Get the fetch option.
     *
     * @return null|integer
     */
    public function getFetchOption()
    {
        return $this->fetchOption;
    }

    /**
     * Mark messages as read when fetching them.
     *
     * @return $this
     */
    public function markAsRead()
    {
        $this->fetchOption = FT_UID;

        return $this;
    }

    /**
     * Do not mark messages as read when fetching them.
     *
     * @return $this
     */
    public function leaveUnread()
    {
        $this->fetchOption = FT_PEEK;

        return $this;
    }

    /**
     * Set the MIME character set to use when searching strings.
     *
     * @param string $charset
     * @return $this
     */
    public function charset($charset)
    {
        $this->charset = $charset;

        return $this;
    }    

    /**
     * Run the search in the folder and return the collection of messages found.
     *
     * @return Support\MessageCollection
     *
     * @throws Exceptions\ConnectionFailedException
     * @throws Exceptions\GetMessagesFailedException
     * @throws Exceptions\MessageSearchValidationException
     */
    public function get()
    {
        return $this->folder->searchMessages(
            $this->statements,
            $this->fetchOption,
            $this->fetchBody,
            $this->charset,
            $this->fetchAttachments
        );
    }
}
