; extensions.conf - the Asterisk dial plan
;
; Static extension configuration file, used by
; the pbx_config module. This is where you configure all your
; inbound and outbound calls in Asterisk.
;
; This configuration file is reloaded
; - With the "dialplan reload" command in the CLI
; - With the "reload" command (that reloads everything) in the CLI

;
; The "General" category is for certain variables.
;
[general]
;
; If static is set to no, or omitted, then the pbx_config will rewrite
; this file when extensions are modified.  Remember that all comments
; made in the file will be lost when that happens.
;
; XXX Not yet implemented XXX
;
static=yes
;
; if static=yes and writeprotect=no, you can save dialplan by
; CLI command "dialplan save" too
;
writeprotect=no
;
; If autofallthrough is set, then if an extension runs out of
; things to do, it will terminate the call with BUSY, CONGESTION
; or HANGUP depending on Asterisk's best guess. This is the default.
;
; If autofallthrough is not set, then if an extension runs out of
; things to do, Asterisk will wait for a new extension to be dialed
; (this is the original behavior of Asterisk 1.0 and earlier).
;
;autofallthrough=no
;
;
;
; If extenpatternmatchnew is set (true, yes, etc), then a new algorithm that uses
; a Trie to find the best matching pattern is used. In dialplans
; with more than about 20-40 extensions in a single context, this
; new algorithm can provide a noticeable speedup.
; With 50 extensions, the speedup is 1.32x
; with 88 extensions, the speedup is 2.23x
; with 138 extensions, the speedup is 3.44x
; with 238 extensions, the speedup is 5.8x
; with 438 extensions, the speedup is 10.4x
; With 1000 extensions, the speedup is ~25x
; with 10,000 extensions, the speedup is 374x
; Basically, the new algorithm provides a flat response
; time, no matter the number of extensions.
;
; By default, the old pattern matcher is used.
;
; ****This is a new feature! *********************
; The new pattern matcher is for the brave, the bold, and
; the desperate. If you have large dialplans (more than about 50 extensions
; in a context), and/or high call volume, you might consider setting
; this value to "yes" !!
; Please, if you try this out, and are forced to return to the
; old pattern matcher, please report your reasons in a bug report
; on https://issues.asterisk.org. We have made good progress in providing
; something compatible with the old matcher; help us finish the job!
;
; This value can be switched at runtime using the cli command "dialplan set extenpatternmatchnew true"
; or "dialplan set extenpatternmatchnew false", so you can experiment to your hearts content.
;
;extenpatternmatchnew=no
;
; If clearglobalvars is set, global variables will be cleared
; and reparsed on a dialplan reload, or Asterisk reload.
;
; If clearglobalvars is not set, then global variables will persist
; through reloads, and even if deleted from the extensions.conf or
; one of its included files, will remain set to the previous value.
;
; NOTE: A complication sets in, if you put your global variables into
; the AEL file, instead of the extensions.conf file. With clearglobalvars
; set, a "reload" will often leave the globals vars cleared, because it
; is not unusual to have extensions.conf (which will have no globals)
; load after the extensions.ael file (where the global vars are stored).
; So, with "reload" in this particular situation, first the AEL file will
; clear and then set all the global vars, then, later, when the extensions.conf
; file is loaded, the global vars are all cleared, and then not set, because
; they are not stored in the extensions.conf file.
;
clearglobalvars=no
;
; User context is where entries from users.conf are registered.  The
; default value is 'default'
;
;userscontext=default
;
; You can include other config files, use the #include command
; (without the ';'). Note that this is different from the "include" command
; that includes contexts within other contexts. The #include command works
; in all asterisk configuration files.
;#include "filename.conf"
;#include <filename.conf>
;#include filename.conf
;
; You can execute a program or script that produces config files, and they
; will be inserted where you insert the #exec command. The #exec command
; works on all asterisk configuration files.  However, you will need to
; activate them within asterisk.conf with the "execincludes" option.  They
; are otherwise considered a security risk.
;#exec /opt/bin/build-extra-contexts.sh
;#exec /opt/bin/build-extra-contexts.sh --foo="bar"
;#exec </opt/bin/build-extra-contexts.sh --foo="bar">
;#exec "/opt/bin/build-extra-contexts.sh --foo=\"bar\""
;

; The "Globals" category contains global variables that can be referenced
; in the dialplan with the GLOBAL dialplan function:
; ${GLOBAL(VARIABLE)}
; ${${GLOBAL(VARIABLE)}} or ${text${GLOBAL(VARIABLE)}} or any hybrid
; Unix/Linux environmental variables can be reached with the ENV dialplan
; function: ${ENV(VARIABLE)}
;
[globals]
SS=$
CONSOLE=Console/dsp				; Console interface for demo
;CONSOLE=DAHDI/1
;CONSOLE=Phone/phone0
IAXINFO=guest					; IAXtel username/password
;IAXINFO=myuser:mypass
TRUNK=DAHDI/G2					; Trunk interface
;
; Note the 'G2' in the TRUNK variable above. It specifies which group (defined
; in chan_dahdi.conf) to dial, i.e. group 2, and how to choose a channel to use
; in the specified group. The four possible options are:
;
; g: select the lowest-numbered non-busy DAHDI channel
;    (aka. ascending sequential hunt group).
; G: select the highest-numbered non-busy DAHDI channel
;    (aka. descending sequential hunt group).
; r: use a round-robin search, starting at the next highest channel than last
;    time (aka. ascending rotary hunt group).
; R: use a round-robin search, starting at the next lowest channel than last
;    time (aka. descending rotary hunt group).
;
TRUNKMSD=1					; MSD digits to strip (usually 1 or 0)
;TRUNK=IAX2/user:pass@provider

;FREENUMDOMAIN=mydomain.com                     ; domain to send on outbound
                                                ; freenum calls (uses outbound-freenum
                                                ; context)

;
; WARNING WARNING WARNING WARNING
; If you load any other extension configuration engine, such as pbx_ael.so,
; your global variables may be overridden by that file.  Please take care to
; use only one location to set global variables, and you will likely save
; yourself a ton of grief.
; WARNING WARNING WARNING WARNING
;
; Any category other than "General" and "Globals" represent
; extension contexts, which are collections of extensions.
;
; Extension names may be numbers, letters, or combinations
; thereof. If an extension name is prefixed by a '_'
; character, it is interpreted as a pattern rather than a
; literal.  In patterns, some characters have special meanings:
;
;   X - any digit from 0-9
;   Z - any digit from 1-9
;   N - any digit from 2-9
;   [1235-9] - any digit in the brackets (in this example, 1,2,3,5,6,7,8,9)
;   . - wildcard, matches anything remaining (e.g. _9011. matches
;	anything starting with 9011 excluding 9011 itself)
;   ! - wildcard, causes the matching process to complete as soon as
;       it can unambiguously determine that no other matches are possible
;
; For example, the extension _NXXXXXX would match normal 7 digit dialings,
; while _1NXXNXXXXXX would represent an area code plus phone number
; preceded by a one.
;
; Each step of an extension is ordered by priority, which must always start
; with 1 to be considered a valid extension.  The priority "next" or "n" means
; the previous priority plus one, regardless of whether the previous priority
; was associated with the current extension or not.  The priority "same" or "s"
; means the same as the previously specified priority, again regardless of
; whether the previous entry was for the same extension.  Priorities may be
; immediately followed by a plus sign and another integer to add that amount
; (most useful with 's' or 'n').  Priorities may then also have an alias, or
; label, in parentheses after their name which can be used in goto situations.
;
; Contexts contain several lines, one for each step of each extension.  One may
; include another context in the current one as well, optionally with a date
; and time.  Included contexts are included in the order they are listed.
; Switches may also be included within a context.  The order of matching within
; a context is always exact extensions, pattern match extensions, includes, and
; switches.  Includes are always processed depth-first.  So for example, if you
; would like a switch "A" to match before context "B", simply put switch "A" in
; an included context "C", where "C" is included in your original context
; before "B".
;
;[context]
;exten => someexten,{priority|label{+|-}offset}[(alias)],application(arg1,arg2,...)
;
; Timing list for includes is
;
;   <time range>,<days of week>,<days of month>,<months>[,<timezone>]
;
; Note that ranges may be specified to wrap around the ends.  Also, minutes are
; fine-grained only down to the closest even minute.
;
;include => daytime,9:00-17:00,mon-fri,*,*
;include => weekend,*,sat-sun,*,*
;include => weeknights,17:02-8:58,mon-fri,*,*
;
; ignorepat can be used to instruct drivers to not cancel dialtone upon receipt
; of a particular pattern.  The most commonly used example is of course '9'
; like this:
;
;ignorepat => 9
;
; so that dialtone remains even after dialing a 9.  Please note that ignorepat
; only works with channels which receive dialtone from the PBX, such as DAHDI,
; Phone, and VPB.  Other channels, such as SIP and MGCP, which generate their
; own dialtone and converse with the PBX only after a number is complete, are
; generally unaffected by ignorepat (unless DISA or another method is used to
; generate a dialtone after answering the channel).
;

;
; Sample entries for extensions.conf
;
;
[dundi-e164-canonical]
;include => stdexten
;
; List canonical entries here
;
;exten => 12564286000,1,Gosub(6000,stdexten(IAX2/foo))
;exten => 12564286000,n,Goto(default,s,1)	; exited Voicemail
;exten => _125642860XX,1,Dial(IAX2/otherbox/${EXTEN:7})

[dundi-e164-customers]
;
; If you are an ITSP or Reseller, list your customers here.
;
;exten => _12564286000,1,Dial(SIP/customer1)
;exten => _12564286001,1,Dial(IAX2/customer2)

[dundi-e164-via-pstn]
;
; If you are freely delivering calls to the PSTN, list them here
;
;exten => _1256428XXXX,1,Dial(DAHDI/G2/${EXTEN:7}) ; Expose all of 256-428
;exten => _1256325XXXX,1,Dial(DAHDI/G2/${EXTEN:7}) ; Ditto for 256-325

[dundi-e164-local]
;
; Context to put your dundi IAX2 or SIP user in for
; full access
;
include => dundi-e164-canonical
include => dundi-e164-customers
include => dundi-e164-via-pstn

[dundi-e164-switch]
;
; Just a wrapper for the switch
;
switch => DUNDi/e164

[dundi-e164-lookup]
;
; Locally to lookup, try looking for a local E.164 solution
; then try DUNDi if we don't have one.
;
include => dundi-e164-local
include => dundi-e164-switch
;
; DUNDi can also be implemented as a Macro instead of using
; the Local channel driver.
;
[macro-dundi-e164]
;
; ARG1 is the extension to Dial
;
; Extension "s" is not a wildcard extension that matches "anything".
; In macros, it is the start extension. In most other cases,
; you have to goto "s" to execute that extension.
;
; Note: In old versions of Asterisk the PBX in some cases defaulted to
; extension "s" when a given extension was wrong (like in AMI originate).
; This is no longer the case.
;
; For wildcard matches, see above - all pattern matches start with
; an underscore.
exten => s,1,Goto(${ARG1},1)
include => dundi-e164-lookup

;
; Here are the entries you need to participate in the IAXTEL
; call routing system.  Most IAXTEL numbers begin with 1-700, but
; there are exceptions.  For more information, and to sign
; up, please go to www.gnophone.com or www.iaxtel.com
;
[iaxtel700]
exten => _91700XXXXXXX,1,Dial(IAX2/${GLOBAL(IAXINFO)}@iaxtel.com/${EXTEN:1}@iaxtel)

;
; The SWITCH statement permits a server to share the dialplan with
; another server. Use with care: Reciprocal switch statements are not
; allowed (e.g. both A -> B and B -> A), and the switched server needs
; to be on-line or else dialing can be severly delayed.
;
[iaxprovider]
;switch => IAX2/user:[key]@myserver/mycontext

[trunkint]
;
; International long distance through trunk
;
exten => _9011.,1,Macro(dundi-e164,${EXTEN:4})
exten => _9011.,n,Dial(${GLOBAL(TRUNK)}/${FILTER(0-9,${EXTEN:${GLOBAL(TRUNKMSD)}})})

[trunkld]
;
; Long distance context accessed through trunk
;
exten => _91NXXNXXXXXX,1,Macro(dundi-e164,${EXTEN:1})
exten => _91NXXNXXXXXX,n,Dial(${GLOBAL(TRUNK)}/${EXTEN:${GLOBAL(TRUNKMSD)}})

[trunklocal]
;
; Local seven-digit dialing accessed through trunk interface
;
exten => _9NXXXXXX,1,Dial(${GLOBAL(TRUNK)}/${EXTEN:${GLOBAL(TRUNKMSD)}})

[trunktollfree]
;
; Long distance context accessed through trunk interface
;
exten => _91800NXXXXXX,1,Dial(${GLOBAL(TRUNK)}/${EXTEN:${GLOBAL(TRUNKMSD)}})
exten => _91888NXXXXXX,1,Dial(${GLOBAL(TRUNK)}/${EXTEN:${GLOBAL(TRUNKMSD)}})
exten => _91877NXXXXXX,1,Dial(${GLOBAL(TRUNK)}/${EXTEN:${GLOBAL(TRUNKMSD)}})
exten => _91866NXXXXXX,1,Dial(${GLOBAL(TRUNK)}/${EXTEN:${GLOBAL(TRUNKMSD)}})

[international]
;
; Master context for international long distance
;
ignorepat => 9
include => longdistance
include => trunkint

[longdistance]
;
; Master context for long distance
;
ignorepat => 9
include => local
include => trunkld

[local]
;
; Master context for local, toll-free, and iaxtel calls only
;
ignorepat => 9
include => default
include => trunklocal
include => iaxtel700
include => trunktollfree
include => iaxprovider

;Include parkedcalls (or the context you define in features conf)
;to enable call parking.
include => parkedcalls
;
; You can use an alternative switch type as well, to resolve
; extensions that are not known here, for example with remote
; IAX switching you transparently get access to the remote
; Asterisk PBX
;
; switch => IAX2/user:password@bigserver/local
;
; An "lswitch" is like a switch but is literal, in that
; variable substitution is not performed at load time
; but is passed to the switch directly (presumably to
; be substituted in the switch routine itself)
;
; lswitch => Loopback/12${EXTEN}@othercontext
;
; An "eswitch" is like a switch but the evaluation of
; variable substitution is performed at runtime before
; being passed to the switch routine.
;
; eswitch => IAX2/context@${CURSERVER}

; The following two contexts are a template to enable the ability to dial
; ISN numbers. For more information about what an ISN number is, please see
; http://www.freenum.org.
;
; This is the dialing hook.  use:
; include => outbound-freenum

[outbound-freenum]
; We'll add more digits as needed. The purpose is to dial things
; like extension numbers at domains (ITAD number) so we're matching
; on lengths of 1 through 6 prior to the separator (the asterisk [*])
;
exten => _X*X!,1,Goto(outbound-freenum2,${EXTEN},1)
exten => _XX*X!,1,Goto(outbound-freenum2,${EXTEN},1)
exten => _XXX*X!,1,Goto(outbound-freenum2,${EXTEN},1)
exten => _XXXX*X!,1,Goto(outbound-freenum2,${EXTEN},1)
exten => _XXXXX*X!,1,Goto(outbound-freenum2,${EXTEN},1)
exten => _XXXXXX*X!,1,Goto(outbound-freenum2,${EXTEN},1)

[outbound-freenum2]
; This is the handler which performs the dialing logic. It is called
; from the [outbound-freenum] context
;
exten => _X!,1,Verbose(2,Performing ISN lookup for ${EXTEN})
same => n,Set(SUFFIX=${CUT(EXTEN,*,2-)})                                ; make sure the suffix is all digits as well
same => n,GotoIf($["${FILTER(0-9,${SUFFIX})}" != "${SUFFIX}"]?fn-CONGESTION,1)
                                                                        ; filter out bad characters per the README-SERIOUSLY.best-practices.txt document
same => n,Set(TIMEOUT(absolute)=10800)
same => n,Set(isnresult=${ENUMLOOKUP(${EXTEN},sip,,1,freenum.org)})     ; perform our lookup with freenum.org
same => n,GotoIf($["${isnresult}" != ""]?from)
same => n,Set(DIALSTATUS=CONGESTION)
same => n,Goto(fn-CONGESTION,1)
same => n(from),Set(__SIPFROMUSER=${CALLERID(num)})
same => n,GotoIf($["${GLOBAL(FREENUMDOMAIN)}" = ""]?dial)               ; check if we set the FREENUMDOMAIN global variable in [global]
same => n,Set(__SIPFROMDOMAIN=${GLOBAL(FREENUMDOMAIN)})                 ;    if we did set it, then we'll use it for our outbound dialing domain
same => n(dial),Dial(SIP/${isnresult},40)
same => n,Goto(fn-${DIALSTATUS},1)

exten => fn-BUSY,1,Busy()

exten => _f[n]-.,1,NoOp(ISN: ${DIALSTATUS})
same => n,Congestion()

[macro-trunkdial]
;
; Standard trunk dial macro (hangs up on a dialstatus that should
; terminate call)
;   ${ARG1} - What to dial
;
exten => s,1,Dial(${ARG1})
exten => s,n,Goto(s-${DIALSTATUS},1)
exten => s-NOANSWER,1,Hangup()
exten => s-BUSY,1,Hangup()
exten => _s-.,1,NoOp

[stdexten]
;
; Standard extension subroutine:
;   ${EXTEN} - Extension
;   ${ARG1} - Device(s) to ring
;   ${ARG2} - Optional context in Voicemail
;
; Note that the current version will drop through to the next priority in the
; case of their pressing '#'.  This gives more flexibility in what do to next:
; you can prompt for a new extension, or drop the call, or send them to a
; general delivery mailbox, or...
;
; The use of the LOCAL() function is purely for convenience.  Any variable
; initially declared as LOCAL() will disappear when the innermost Gosub context
; in which it was declared returns.  Note also that you can declare a LOCAL()
; variable on top of an existing variable, and its value will revert to its
; previous value (before being declared as LOCAL()) upon Return.
;
exten => _X.,50000(stdexten),NoOp(Start stdexten)
exten => _X.,n,Set(LOCAL(ext)=${EXTEN})
exten => _X.,n,Set(LOCAL(dev)=${ARG1})
exten => _X.,n,Set(LOCAL(cntx)=${ARG2})
exten => _X.,n,Set(LOCAL(mbx)=${ext}${IF($[!${ISNULL(${cntx})}]?@${cntx})})
exten => _X.,n,Dial(${dev},20)				; Ring the interface, 20 seconds maximum
exten => _X.,n,Goto(stdexten-${DIALSTATUS},1)		; Jump based on status (NOANSWER,BUSY,CHANUNAVAIL,CONGESTION,ANSWER)

exten => stdexten-NOANSWER,1,VoiceMail(${mbx},u)	; If unavailable, send to voicemail w/ unavail announce
exten => stdexten-NOANSWER,n,Return()			; If they press #, return to start

exten => stdexten-BUSY,1,VoiceMail(${mbx},b)		; If busy, send to voicemail w/ busy announce
exten => stdexten-BUSY,n,Return()			; If they press #, return to start

exten => _stde[x]te[n]-.,1,Goto(stdexten-NOANSWER,1)	; Treat anything else as no answer

exten => a,1,VoiceMailMain(${mbx})			; If they press *, send the user into VoicemailMain
exten => a,n,Return()

[stdPrivacyexten]
;
; Standard extension subroutine:
;   ${ARG1} - Extension
;   ${ARG2} - Device(s) to ring
;   ${ARG3} - Optional DONTCALL context name to jump to (assumes the s,1 extension-priority)
;   ${ARG4} - Optional TORTURE context name to jump to (assumes the s,1 extension-priority)`
;   ${ARG5} - Context in voicemail (if empty, then "default")
;
; See above note in stdexten about priority handling on exit.
;
exten => _X.,60000(stdPrivacyexten),NoOp(Start stdPrivacyexten)
exten => _X.,n,Set(LOCAL(ext)=${ARG1})
exten => _X.,n,Set(LOCAL(dev)=${ARG2})
exten => _X.,n,Set(LOCAL(dontcntx)=${ARG3})
exten => _X.,n,Set(LOCAL(tortcntx)=${ARG4})
exten => _X.,n,Set(LOCAL(cntx)=${ARG5})

exten => _X.,n,Set(LOCAL(mbx)="${ext}"$["${cntx}" ? "@${cntx}" :: ""])
exten => _X.,n,Dial(${dev},20,p)			; Ring the interface, 20 seconds maximum, call screening
						; option (or use P for databased call _X.creening)
exten => _X.,n,Goto(stdexten-${DIALSTATUS},1)		; Jump based on status (NOANSWER,BUSY,CHANUNAVAIL,CONGESTION,ANSWER)

exten => stdexten-NOANSWER,1,VoiceMail(${mbx},u)	; If unavailable, send to voicemail w/ unavail announce
exten => stdexten-NOANSWER,n,NoOp(Finish stdPrivacyexten NOANSWER)
exten => stdexten-NOANSWER,n,Return()			; If they press #, return to start

exten => stdexten-BUSY,1,VoiceMail(${mbx},b)		; If busy, send to voicemail w/ busy announce
exten => stdexten-BUSY,n,NoOp(Finish stdPrivacyexten BUSY)
exten => stdexten-BUSY,n,Return()			; If they press #, return to start

exten => stdexten-DONTCALL,1,Goto(${dontcntx},s,1)	; Callee chose to send this call to a polite "Don't call again" script.

exten => stdexten-TORTURE,1,Goto(${tortcntx},s,1)	; Callee chose to send this call to a telemarketer torture script.

exten => _stde[x]te[n]-.,1,Goto(stdexten-NOANSWER,1)	; Treat anything else as no answer

exten => a,1,VoiceMailMain(${mbx})		; If they press *, send the user into VoicemailMain
exten => a,n,Return()

[macro-page]
;
; Paging macro:
;
;       Check to see if SIP device is in use and DO NOT PAGE if they are
;
;   ${ARG1} - Device to page

exten => s,1,ChanIsAvail(${ARG1},s)			; s is for ANY call
exten => s,n,GotoIf($[${AVAILSTATUS} = "1"]?autoanswer:fail)
exten => s,n(autoanswer),Set(_ALERT_INFO="RA")			; This is for the PolyComs
exten => s,n,SIPAddHeader(Call-Info: Answer-After=0)	; This is for the Grandstream, Snoms, and Others
exten => s,n,NoOp()					; Add others here and Post on the Wiki!!!!
exten => s,n,Dial(${ARG1})
exten => s,n(fail),Hangup()


[demo]
include => stdexten
;
; We start with what to do when a call first comes in.
;
exten => s,1,Wait(1)			; Wait a second, just for fun
exten => s,n,Answer()			; Answer the line
exten => s,n,Set(TIMEOUT(digit)=5)	; Set Digit Timeout to 5 seconds
exten => s,n,Set(TIMEOUT(response)=10)	; Set Response Timeout to 10 seconds
exten => s,n(restart),BackGround(demo-congrats)	; Play a congratulatory message
exten => s,n(instruct),BackGround(demo-instruct)	; Play some instructions
exten => s,n,WaitExten()		; Wait for an extension to be dialed.

exten => 2,1,BackGround(demo-moreinfo)	; Give some more information.
exten => 2,n,Goto(s,instruct)

exten => 3,1,Set(CHANNEL(language)=fr)		; Set language to french
exten => 3,n,Goto(s,restart)		; Start with the congratulations

exten => 1000,1,Goto(default,s,1)
;
; We also create an example user, 1234, who is on the console and has
; voicemail, etc.
;
exten => 1234,1,Playback(transfer,skip)		; "Please hold while..."
					; (but skip if channel is not up)
exten => 1234,n,Gosub(${EXTEN},stdexten(${GLOBAL(CONSOLE)}))
exten => 1234,n,Goto(default,s,1)		; exited Voicemail

exten => 1235,1,VoiceMail(1234,u)		; Right to voicemail

exten => 1236,1,Dial(Console/dsp)		; Ring forever
exten => 1236,n,VoiceMail(1234,b)		; Unless busy

;
; # for when they're done with the demo
;
exten => #,1,Playback(demo-thanks)	; "Thanks for trying the demo"
exten => #,n,Hangup()			; Hang them up.

;
; A timeout and "invalid extension rule"
;
exten => t,1,Goto(#,1)			; If they take too long, give up
exten => i,1,Playback(invalid)		; "That's not valid, try again"

;
; Create an extension, 500, for dialing the
; Asterisk demo.
;
exten => 500,1,Playback(demo-abouttotry)	; Let them know what's going on
exten => 500,n,Dial(IAX2/guest@pbx.digium.com/s@default)	; Call the Asterisk demo
exten => 500,n,Playback(demo-nogo)	; Couldn't connect to the demo site
exten => 500,n,Goto(s,6)		; Return to the start over message.

;
; Create an extension, 600, for evaluating echo latency.
;
exten => 600,1,Playback(demo-echotest)	; Let them know what's going on
exten => 600,n,Echo()			; Do the echo test
exten => 600,n,Playback(demo-echodone)	; Let them know it's over
exten => 600,n,Goto(s,6)		; Start over

;
;	You can use the Macro Page to intercom a individual user
exten => 76245,1,Macro(page,SIP/Grandstream1)
; or if your peernames are the same as extensions
exten => _7XXX,1,Macro(page,SIP/${EXTEN})
;
;
; System Wide Page at extension 7999
;
exten => 7999,1,Set(TIMEOUT(absolute)=60)
exten => 7999,2,Page(Local/Grandstream1@page&Local/Xlite1@page&Local/1234@page/n,d)

; Give voicemail at extension 8500
;
exten => 8500,1,VoiceMailMain()
exten => 8500,n,Goto(s,6)
;
; Here's what a phone entry would look like (IXJ for example)
;
;exten => 1265,1,Dial(Phone/phone0,15)
;exten => 1265,n,Goto(s,5)

;
;	The page context calls up the page macro that sets variables needed for auto-answer
;	It is in is own context to make calling it from the Page() application as simple as
;	Local/{peername}@page
;
[page]
exten => _X.,1,Macro(page,SIP/${EXTEN})

;[mainmenu]
;
; Example "main menu" context with submenu
;
;exten => s,1,Answer
;exten => s,n,Background(thanks)		; "Thanks for calling press 1 for sales, 2 for support, ..."
;exten => s,n,WaitExten
;exten => 1,1,Goto(submenu,s,1)
;exten => 2,1,Hangup
;include => default
;
;[submenu]
;exten => s,1,Ringing					; Make them comfortable with 2 seconds of ringback
;exten => s,n,Wait,2
;exten => s,n,Background(submenuopts)	; "Thanks for calling the sales department.  Press 1 for steve, 2 for..."
;exten => s,n,WaitExten
;exten => 1,1,Goto(default,steve,1)
;exten => 2,1,Goto(default,mark,2)



; https://www.voip-info.org/asterisk-config-extensionsconf/
; v...context
[anonymous]
<?php
if ($_ENV["PHONE_NUMBER_APPFREE_LOCAL"]) {
?>
exten => <?= $_ENV["PHONE_NUMBER_APPFREE_LOCAL"] ?>,1,Noop()
same => n,Stasis(appfree-app_local)
same => n,Hangup()
<?php
}
if ($_ENV["PHONE_NUMBER_APPFREE_STAGING"]) {
?>
exten => <?= $_ENV["PHONE_NUMBER_APPFREE_STAGING"] ?>,1,Noop()
same => n,Stasis(appfree-app_staging)
same => n,Hangup()
<?php
}
if ($_ENV["PHONE_NUMBER_APPFREE_PROD"]) {

?>
exten => <?= $_ENV["PHONE_NUMBER_APPFREE_PROD"] ?>,1,Noop()
same => n,Stasis(appfree-app_prod)
same => n,Hangup()
<?php
}