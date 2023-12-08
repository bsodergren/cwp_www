<?php
/**
 * Command like Metatag writer for video files.
 */

namespace CWPCLI\Locales;

trait Lang
{
    public const L__APP_DEFAULT_CMD = 'The command to execute';
    public const L__APP_DEFAULT_HELP = 'Display help for the given command. When no command is given display help for the <info>%%CMD%%</info> command';
    public const L__APP_DEFAULT_QUIET = 'Do not output any message';
    public const L__APP_DEFAULT_VERBOSE = 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug';
    public const L__APP_DEFAULT_VERSION = 'Display this application version';
    public const L__APP_DEFAULT_NOASK = 'Do not ask any interactive question';
    public const L__DEFAULT_FILELIST = 'Comma separeted list of files';
    public const L__DEFAULT_MAX = 'Max number of results';
    public const L__DEFAULT_RANGE = 'Range files to process';
    public const L__DEFAULT_FILENUMBER = 'The index number of the file to work on';
    public const L__DEFAULT_LIST = 'List all files instead of refreshing the output';
    public const L__DEFAULT_NUMBEROFFILES = 'Lists the number of files ';
    public const L__DEFAULT_TEST_CMD = 'Dont do database or process activity';
    public const L__DEFAULT_TEST_PREVIEW = 'Peview Changes';
    public const L__DEFAULT_TEST_TIME = 'Profile timing';
    public const L__DEFAULT_TEST_DUMP = 'No idea';
    public const L__DEFAULT_TEST_FLUSH = 'Flush cache';

    public const L__PHDB_ASK_CONTINUE = 'Continue with %%NEXT%%? yY|nN|A|N ';

    // public const L__DB_VIDEO_COUNT      = 'There are currently <info>%%VID%%</info> files in the DB';
    public const L__META_TITLE = '%%TXT%% Title';
    public const L__META_GENRE = '%%TXT%% Genre';
    public const L__META_STUDIO = '%%TXT%% Studio';
    public const L__META_ARTIST = '%%TXT%% Artist';
    public const L__META_KEYWORD = '%%TXT%% Keyword';
    public const L__META_ONLY = '%%TXT%% Only selected metatags ';
}
