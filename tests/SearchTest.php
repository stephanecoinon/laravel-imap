<?php

namespace Tests;

use Carbon\Carbon;
use Webklex\IMAP\Folder;
use Webklex\IMAP\Search;

class SearchTest extends TestCase
{
    /**
     * Helper making a new Search instance.
     *
     * @return \Webklex\IMAP\Search
     */
    public function makeSearch()
    {
        return new Search(new FakeFolder);
    }

    /** @test */
    public function adding_a_statement_containing_key_and_value()
    {
        $s = $this->makeSearch()->addStatement('key', 'value');

        $this->assertEquals([['key', 'value']], $s->getStatements());
    }

    /** @test */
    public function adding_a_statement_containing_a_carbon_instance()
    {
        $s = $this->makeSearch()->addStatement('key', Carbon::create(2018, 4, 8));

        $this->assertEquals([['key', '08 Apr 18']], $s->getStatements());
    }

    /** @test */
    public function adding_a_statement_containing_only_a_key()
    {
        $s = $this->makeSearch()->addStatement('key');

        $this->assertEquals([['key']], $s->getStatements());
    }

    /** @test */
    public function adding_two_statements()
    {
        $s = $this->makeSearch()
            ->addStatement('foo', 'bar')
            ->addStatement('baz');

        $this->assertEquals([
            ['foo', 'bar'],
            ['baz']
        ], $s->getStatements());
    }
    
    /** @test */
    public function all()
    {
        $s = $this->makeSearch()->all();

        $this->assertEquals([['ALL']], $s->getStatements());
    }

    /** @test */
    public function answered()
    {
        $s = $this->makeSearch()->answered();

        $this->assertEquals([['ANSWERED']], $s->getStatements());
    }

    /** @test */
    public function unanswered()
    {
        $s = $this->makeSearch()->unanswered();

        $this->assertEquals([['UNANSWERED']], $s->getStatements());
    }

    /** @test */
    public function before_a_carbon_instance()
    {
        $s = $this->makeSearch()->before(Carbon::create(2018, 4, 8));

        $this->assertEquals([['BEFORE', '08 Apr 18']], $s->getStatements());
    }

    /** @test */
    public function body()
    {
        $s = $this->makeSearch()->body('lorem ipsum');

        $this->assertEquals([['BODY', 'lorem ipsum']], $s->getStatements());
    }

    /** @test */
    public function deleted()
    {
        $s = $this->makeSearch()->deleted();

        $this->assertEquals([['DELETED']], $s->getStatements());
    }

    /** @test */
    public function undeleted()
    {
        $s = $this->makeSearch()->undeleted();

        $this->assertEquals([['UNDELETED']], $s->getStatements());
    }

    /** @test */
    public function flagged()
    {
        $s = $this->makeSearch()->flagged();

        $this->assertEquals([['FLAGGED']], $s->getStatements());
    }

    /** @test */
    public function unflagged()
    {
        $s = $this->makeSearch()->unflagged();

        $this->assertEquals([['UNFLAGGED']], $s->getStatements());
    }

    /** @test */
    public function from()
    {
        $s = $this->makeSearch()->from('john@example.com');

        $this->assertEquals([['FROM', 'john@example.com']], $s->getStatements());
    }

    /** @test */
    public function to()
    {
        $s = $this->makeSearch()->to('john@example.com');

        $this->assertEquals([['TO', 'john@example.com']], $s->getStatements());
    }

    /** @test */
    public function cc()
    {
        $s = $this->makeSearch()->cc('john@example.com');

        $this->assertEquals([['CC', 'john@example.com']], $s->getStatements());
    }

    /** @test */
    public function bcc()
    {
        $s = $this->makeSearch()->bcc('john@example.com');

        $this->assertEquals([['BCC', 'john@example.com']], $s->getStatements());
    }

    /** @test */
    public function contains_keyword()
    {
        $s = $this->makeSearch()->containsKeyword('foo');

        $this->assertEquals([['KEYWORD', 'foo']], $s->getStatements());
    }

    /** @test */
    public function does_not_contain_keyword()
    {
        $s = $this->makeSearch()->doesNotContainKeyword('foo');

        $this->assertEquals([['UNKEYWORD', 'foo']], $s->getStatements());
    }
    
    /** @test */
    public function new()
    {
        $s = $this->makeSearch()->new();

        $this->assertEquals([['NEW']], $s->getStatements());
    }
    
    /** @test */
    public function old()
    {
        $s = $this->makeSearch()->old();

        $this->assertEquals([['OLD']], $s->getStatements());
    }
    
    /** @test */
    public function recent()
    {
        $s = $this->makeSearch()->recent();

        $this->assertEquals([['RECENT']], $s->getStatements());
    }
    
    /** @test */
    public function seen()
    {
        $s = $this->makeSearch()->seen();

        $this->assertEquals([['SEEN']], $s->getStatements());
    }

    /** @test */
    public function read()
    {
        $s = $this->makeSearch()->read();

        $this->assertEquals([['SEEN']], $s->getStatements());
    }

    /** @test */
    public function unseen()
    {
        $s = $this->makeSearch()->unseen();

        $this->assertEquals([['UNSEEN']], $s->getStatements());
    }

    /** @test */
    public function unread()
    {
        $s = $this->makeSearch()->unread();

        $this->assertEquals([['UNSEEN']], $s->getStatements());
    }
    
    /** @test */
    public function since_a_carbon_instance()
    {
        $s = $this->makeSearch()->since(Carbon::create(2018, 4, 8));

        $this->assertEquals([['SINCE', '08 Apr 18']], $s->getStatements());
    }

    /** @test */
    public function subject()
    {
        $s = $this->makeSearch()->subject('foo');

        $this->assertEquals([['SUBJECT', 'foo']], $s->getStatements());
    }

    /** @test */
    public function text()
    {
        $s = $this->makeSearch()->text('foo');

        $this->assertEquals([['TEXT', 'foo']], $s->getStatements());
    }

    /** @test */
    public function fetch_option_is_read_from_configuration_by_default()
    {
        // null means reading option from configuration
        $this->assertNull($this->makeSearch()->getFetchOption());
    }

    /** @test */
    public function marking_messages_as_read_when_fetching_them()
    {
        $this->assertEquals(
            FT_UID,
            $this->makeSearch()->markAsRead()->getFetchOption()
        );
    }

    /** @test */
    public function keeping_messages_unread_when_fetching_them()
    {
        $this->assertEquals(
            FT_PEEK,
            $this->makeSearch()->leaveUnread()->getFetchOption()
        );
    }

    /** @test */
    public function setting_charset()
    {
        $this->assertEquals('UTF-8', $this->makeSearch()->charset('UTF-8')->charset);
    }
}


class FakeFolder extends Folder
{
    public function __construct(Client $client = null, $folder = null)
    {
    }
}
