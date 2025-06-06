;
; SIP Configuration example for Asterisk
;
; Note: Please read the security documentation for Asterisk in order to
; 	understand the risks of installing Asterisk with the sample
;	configuration. If your Asterisk is installed on a public
;	IP address connected to the Internet, you will want to learn
;	about the various security settings BEFORE you start
;	Asterisk.
;
;	Especially note the following settings:
;		- allowguest (default enabled)
;		- permit/deny/acl - IP address filters
;		- contactpermit/contactdeny/contactacl - IP address filters for registrations
;		- context - Which set of services you offer various users
;
; SIP dial strings
; ----------------------------------------------------------
; In the dialplan (extensions.conf) you can use several
; syntaxes for dialing SIP devices.
;        SIP/devicename
;        SIP/username@domain   (SIP uri)
;        SIP/username[:password[:md5secret[:authname[:transport]]]]@host[:port]
;        SIP/devicename/extension
;        SIP/devicename/extension/IPorHost
;        SIP/username@domain//IPorHost
;
;
; Devicename
;        devicename is defined as a peer in a section below.
;
; username@domain
;        Call any SIP user on the Internet
;        (Don't forget to enable DNS SRV records if you want to use this)
;
; devicename/extension
;        If you define a SIP proxy as a peer below, you may call
;        SIP/proxyhostname/user or SIP/user@proxyhostname
;        where the proxyhostname is defined in a section below
;        This syntax also works with ATA's with FXO ports
;
; SIP/username[:password[:md5secret[:authname]]]@host[:port]
;        This form allows you to specify password or md5secret and authname
;        without altering any authentication data in config.
;        Examples:
;
;        SIP/*98@mysipproxy
;        SIP/sales:topsecret::account02@domain.com:5062
;        SIP/12345678::bc53f0ba8ceb1ded2b70e05c3f91de4f:myname@192.168.0.1
;
; IPorHost
;        The next server for this call regardless of domain/peer
;
; All of these dial strings specify the SIP request URI.
; In addition, you can specify a specific To: header by adding an
; exclamation mark after the dial string, like
;
;         SIP/sales@mysipproxy!sales@edvina.net
;
; A new feature for 1.8 allows one to specify a host or IP address to use
; when routing the call. This is typically used in tandem with func_srv if
; multiple methods of reaching the same domain exist. The host or IP address
; is specified after the third slash in the dialstring. Examples:
;
; SIP/devicename/extension/IPorHost
; SIP/username@domain//IPorHost
;
; CLI Commands
; -------------------------------------------------------------
; Useful CLI commands to check peers/users:
;   sip show peers               Show all SIP peers (including friends)
;   sip show registry            Show status of hosts we register with
;
;   sip set debug on             Show all SIP messages
;
;   sip reload                   Reload configuration file
;   sip show settings            Show the current channel configuration
;
; ------ Naming devices ------------------------------------------------------
;
; When naming devices, make sure you understand how Asterisk matches calls
; that come in.
;	1. Asterisk checks the SIP From: address username and matches against
;	   names of devices with type=user
;	   The name is the text between square brackets [name]
;	2. Asterisk checks the From: addres and matches the list of devices
;	   with a type=peer
;	3. Asterisk checks the IP address (and port number) that the INVITE
;	   was sent from and matches against any devices with type=peer
;
; Don't mix extensions with the names of the devices. Devices need a unique
; name. The device name is *not* used as phone numbers. Phone numbers are
; anything you declare as an extension in the dialplan (extensions.conf).
;
; When setting up trunks, make sure there's no risk that any From: username
; (caller ID) will match any of your device names, because then Asterisk
; might match the wrong device.
;
; Note: The parameter "username" is not the username and in most cases is
;       not needed at all. Check below. In later releases, it's renamed
;       to "defaultuser" which is a better name, since it is used in
;       combination with the "defaultip" setting.
; ----------------------------------------------------------------------------

; ** Old configuration options **
; The "call-limit" configuation option is considered old is replaced
; by new functionality. To enable callcounters, you use the new 
; "callcounter" setting (for extension states in queue and subscriptions)
; You are encouraged to use the dialplan groupcount functionality
; to enforce call limits instead of using this channel-specific method.
; You can still set limits per device in sip.conf or in a database by using
; "setvar" to set variables that can be used in the dialplan for various limits.

[general]


register => 5036831t0:qtgRu4mKy4o3@sipconnect.sipgate.de
;register => 5036831e0:GWVVFH@sipgate.de


context=default                  ; Default context for incoming calls. Defaults to 'default'
;allowguest=no                  ; Allow or reject guest calls (default is yes)
				; If your Asterisk is connected to the Internet
				; and you have allowguest=yes
				; you want to check which services you offer everyone
				; out there, by enabling them in the default context (see below).
;match_auth_username=yes        ; if available, match user entry using the
                                ; 'username' field from the authentication line
                                ; instead of the From: field.
allowoverlap=no                 ; Disable overlap dialing support. (Default is yes)
;allowoverlap=yes               ; Enable RFC3578 overlap dialing support.
                                ; Can use the Incomplete application to collect the
                                ; needed digits from an ambiguous dialplan match.
;allowoverlap=dtmf              ; Enable overlap dialing support using DTMF delivery
                                ; methods (inband, RFC2833, SIP INFO) in the early
                                ; media phase.  Uses the Incomplete application to
                                ; collect the needed digits.
;allowtransfer=no               ; Disable all transfers (unless enabled in peers or users)
                                ; Default is enabled. The Dial() options 't' and 'T' are not
                                ; related as to whether SIP transfers are allowed or not.
;realm=mydomain.tld             ; Realm for digest authentication
                                ; defaults to "asterisk". If you set a system name in
                                ; asterisk.conf, it defaults to that system name
                                ; Realms MUST be globally unique according to RFC 3261
                                ; Set this to your host name or domain name
;domainsasrealm=no              ; Use domains list as realms
                                ; You can serve multiple Realms specifying several
                                ; 'domain=...' directives (see below). 
                                ; In this case Realm will be based on request 'From'/'To' header
                                ; and should match one of domain names.
                                ; Otherwise default 'realm=...' will be used.
;recordonfeature=automixmon	; Default feature to use when receiving 'Record: on' header
				; from an INFO message. Defaults to 'automon'. Works with
				; dynamic features. Feature must be usable on requesting
				; channel for it to work. Setting this value to a blank
				; will disable it.
;recordofffeature=automixmon	; Default feature to use when receiving 'Record: off' header
				; from an INFO message. Defaults to 'automon'. Works with
				; dynamic features. Feature must be usable on requesting
				; channel for it to work. Setting this value to a blank
				; will disable it.

; With the current situation, you can do one of four things:
;  a) Listen on a specific IPv4 address.      Example: bindaddr=192.0.2.1
;  b) Listen on a specific IPv6 address.      Example: bindaddr=2001:db8::1
;  c) Listen on the IPv4 wildcard.            Example: bindaddr=0.0.0.0
;  d) Listen on the IPv4 and IPv6 wildcards.  Example: bindaddr=::
; (You can choose independently for UDP, TCP, and TLS, by specifying different values for
; "udpbindaddr", "tcpbindaddr", and "tlsbindaddr".)
; (Note that using bindaddr=:: will show only a single IPv6 socket in netstat.
;  IPv4 is supported at the same time using IPv4-mapped IPv6 addresses.)
;
; You may optionally add a port number. (The default is port 5060 for UDP and TCP, 5061
; for TLS).
;   IPv4 example: bindaddr=0.0.0.0:5062
;   IPv6 example: bindaddr=[::]:5062
;
; The address family of the bound UDP address is used to determine how Asterisk performs
; DNS lookups. In cases a) and c) above, only A records are considered. In case b), only
; AAAA records are considered. In case d), both A and AAAA records are considered. Note,
; however, that Asterisk ignores all records except the first one. In case d), when both A
; and AAAA records are available, either an A or AAAA record will be first, and which one
; depends on the operating system. On systems using glibc, AAAA records are given
; priority.

udpbindaddr=0.0.0.0             ; IP address to bind UDP listen socket to (0.0.0.0 binds to all)
                                ; Optionally add a port number, 192.168.1.1:5062 (default is port 5060)

; When a dialog is started with another SIP endpoint, the other endpoint
; should include an Allow header telling us what SIP methods the endpoint
; implements. However, some endpoints either do not include an Allow header
; or lie about what methods they implement. In the former case, Asterisk
; makes the assumption that the endpoint supports all known SIP methods.
; If you know that your SIP endpoint does not provide support for a specific
; method, then you may provide a comma-separated list of methods that your
; endpoint does not implement in the disallowed_methods option. Note that 
; if your endpoint is truthful with its Allow header, then there is no need 
; to set this option. This option may be set in the general section or may
; be set per endpoint. If this option is set both in the general section and
; in a peer section, then the peer setting completely overrides the general
; setting (i.e. the result is *not* the union of the two options).
;
; Note also that while Asterisk currently will parse an Allow header to learn
; what methods an endpoint supports, the only actual use for this currently
; is for determining if Asterisk may send connected line UPDATE requests and
; MESSAGE requests. Its use may be expanded in the future.
;
; disallowed_methods = UPDATE

;
; Note that the TCP and TLS support for chan_sip is currently considered
; experimental.  Since it is new, all of the related configuration options are
; subject to change in any release.  If they are changed, the changes will
; be reflected in this sample configuration file, as well as in the UPGRADE.txt file.
;
tcpenable=no                    ; Enable server for incoming TCP connections (default is no)
tcpbindaddr=0.0.0.0             ; IP address for TCP server to bind to (0.0.0.0 binds to all interfaces)
                                ; Optionally add a port number, 192.168.1.1:5062 (default is port 5060)

;tlsenable=no                   ; Enable server for incoming TLS (secure) connections (default is no)
;tlsbindaddr=0.0.0.0            ; IP address for TLS server to bind to (0.0.0.0) binds to all interfaces)
                                ; Optionally add a port number, 192.168.1.1:5063 (default is port 5061)
                                ; Remember that the IP address must match the common name (hostname) in the
                                ; certificate, so you don't want to bind a TLS socket to multiple IP addresses.
                                ; For details how to construct a certificate for SIP see 
                                ; http://tools.ietf.org/html/draft-ietf-sip-domain-certs

;tcpauthtimeout = 30            ; tcpauthtimeout specifies the maximum number
				; of seconds a client has to authenticate.  If
				; the client does not authenticate beofre this
				; timeout expires, the client will be
                                ; disconnected. (default: 30 seconds)

;tcpauthlimit = 100             ; tcpauthlimit specifies the maximum number of
				; unauthenticated sessions that will be allowed
                                ; to connect at any given time. (default: 100)

;websocket_enabled = true       ; Set to false to prevent chan_sip from listening to websockets.  This
                                ; is neeeded when using chan_sip and res_pjsip_transport_websockets on
                                ; the same system.

;websocket_write_timeout = 100  ; Default write timeout to set on websocket transports.
                                ; This value may need to be adjusted for connections where
                                ; Asterisk must write a substantial amount of data and the
                                ; receiving clients are slow to process the received information.
                                ; Value is in milliseconds; default is 100 ms.

transport=udp                   ; Set the default transports.  The order determines the primary default transport.
                                ; If tcpenable=no and the transport set is tcp, we will fallback to UDP.

srvlookup=yes                   ; Enable DNS SRV lookups on outbound calls
                                ; Note: Asterisk only uses the first host
                                ; in SRV records
                                ; Disabling DNS SRV lookups disables the
                                ; ability to place SIP calls based on domain
                                ; names to some other SIP users on the Internet
                                ; Specifying a port in a SIP peer definition or
                                ; when dialing outbound calls will supress SRV
                                ; lookups for that peer or call.

;pedantic=yes                   ; Enable checking of tags in headers,
                                ; international character conversions in URIs
                                ; and multiline formatted headers for strict
                                ; SIP compatibility (defaults to "yes")

; See https://wiki.asterisk.org/wiki/display/AST/IP+Quality+of+Service for a description of these parameters.
;tos_sip=cs3                    ; Sets TOS for SIP packets.
;tos_audio=ef                   ; Sets TOS for RTP audio packets.
;tos_video=af41                 ; Sets TOS for RTP video packets.
;tos_text=af41                  ; Sets TOS for RTP text packets.

;cos_sip=3                      ; Sets 802.1p priority for SIP packets.
;cos_audio=5                    ; Sets 802.1p priority for RTP audio packets.
;cos_video=4                    ; Sets 802.1p priority for RTP video packets.
;cos_text=3                     ; Sets 802.1p priority for RTP text packets.

;maxexpiry=3600                 ; Maximum allowed time of incoming registrations (seconds)
;minexpiry=60                   ; Minimum length of registrations (default 60)
;defaultexpiry=120              ; Default length of incoming/outgoing registration
;submaxexpiry=3600              ; Maximum allowed time of incoming subscriptions (seconds), default: maxexpiry
;subminexpiry=60                ; Minimum length of subscriptions, default: minexpiry
;mwiexpiry=3600                 ; Expiry time for outgoing MWI subscriptions
;maxforwards=70			; Setting for the SIP Max-Forwards: header (loop prevention)
				; Default value is 70
;qualifyfreq=60                 ; Qualification: How often to check for the host to be up in seconds
				; and reported in milliseconds with sip show settings.
                                ; Set to low value if you use low timeout for NAT of UDP sessions
				; Default: 60
;qualifygap=100			; Number of milliseconds between each group of peers being qualified
				; Default: 100
;qualifypeers=1			; Number of peers in a group to be qualified at the same time
				; Default: 1
;keepalive=60                   ; Interval at which keepalive packets should be sent to a peer
				; Valid options are yes (60 seconds), no, or the number of seconds.
                                ; Default: 0
;notifymimetype=text/plain      ; Allow overriding of mime type in MWI NOTIFY
;buggymwi=no                    ; Cisco SIP firmware doesn't support the MWI RFC
                                ; fully. Enable this option to not get error messages
                                ; when sending MWI to phones with this bug.
;mwi_from=asterisk              ; When sending MWI NOTIFY requests, use this setting in
                                ; the From: header as the "name" portion. Also fill the
			        ; "user" portion of the URI in the From: header with this
			        ; value if no fromuser is set
			        ; Default: empty
;vmexten=voicemail              ; dialplan extension to reach mailbox sets the
                                ; Message-Account in the MWI notify message
                                ; defaults to "asterisk"

; Codec negotiation
;
; When Asterisk is receiving a call, the codec will initially be set to the
; first codec in the allowed codecs defined for the user receiving the call
; that the caller also indicates that it supports. But, after the caller
; starts sending RTP, Asterisk will switch to using whatever codec the caller
; is sending.
;
; When Asterisk is placing a call, the codec used will be the first codec in
; the allowed codecs that the callee indicates that it supports. Asterisk will
; *not* switch to whatever codec the callee is sending.
;
;preferred_codec_only=yes       ; Respond to a SIP invite with the single most preferred codec
                                ; rather than advertising all joint codec capabilities. This
                                ; limits the other side's codec choice to exactly what we prefer.

;disallow=all                   ; First disallow all codecs
;allow=ulaw                     ; Allow codecs in order of preference
;allow=ilbc                     ; see https://wiki.asterisk.org/wiki/display/AST/RTP+Packetization
				; for framing options
;autoframing=yes		; Set packetization based on the remote endpoint's (ptime)
				; preferences. Defaults to no.
;
; This option specifies a preference for which music on hold class this channel
; should listen to when put on hold if the music class has not been set on the
; channel with Set(CHANNEL(musicclass)=whatever) in the dialplan, and the peer
; channel putting this one on hold did not suggest a music class.
;
; This option may be specified globally, or on a per-user or per-peer basis.
;
;mohinterpret=default
;
; This option specifies which music on hold class to suggest to the peer channel
; when this channel places the peer on hold. It may be specified globally or on
; a per-user or per-peer basis.
;
;mohsuggest=default
;
;parkinglot=plaza               ; Sets the default parking lot for call parking
                                ; This may also be set for individual users/peers
                                ; Parkinglots are configured in features.conf
;language=en                    ; Default language setting for all users/peers
                                ; This may also be set for individual users/peers
;tonezone=se			; Default tonezone for all users/peers
                                ; This may also be set for individual users/peers

relaxdtmf=yes                  ; Relax dtmf handling
;trustrpid = no                 ; If Remote-Party-ID should be trusted
;sendrpid = yes                 ; If Remote-Party-ID should be sent (defaults to no)
;sendrpid = rpid                ; Use the "Remote-Party-ID" header
                                ; to send the identity of the remote party
                                ; This is identical to sendrpid=yes
;sendrpid = pai                 ; Use the "P-Asserted-Identity" header
                                ; to send the identity of the remote party
;rpid_update = no               ; In certain cases, the only method by which a connected line
                                ; change may be immediately transmitted is with a SIP UPDATE request.
                                ; If communicating with another Asterisk server, and you wish to be able
                                ; transmit such UPDATE messages to it, then you must enable this option.
                                ; Otherwise, we will have to wait until we can send a reinvite to
                                ; transmit the information.
;trust_id_outbound = no         ; Controls whether or not we trust this peer with private identity
                                ; information (when the remote party has callingpres=prohib or equivalent).
                                ; no - RPID/PAI headers will not be included for private peer information
                                ; yes - RPID/PAI headers will include the private peer information. Privacy
                                ;       requirements will be indicated in a Privacy header for sendrpid=pai
                                ; legacy - RPID/PAI will be included for private peer information. In the
                                ;       case of sendrpid=pai, private data that would be included in them
                                ;       will be anonymized. For sendrpid=rpid, private data may be included
                                ;       but the remote party's domain will be anonymized. The way legacy
                                ;       behaves may violate RFC-3325, but it follows historic behavior.
                                ; This option is set to 'legacy' by default
;prematuremedia=no              ; Some ISDN links send empty media frames before 
                                ; the call is in ringing or progress state. The SIP 
                                ; channel will then send 183 indicating early media
                                ; which will be empty - thus users get no ring signal.
                                ; Setting this to "yes" will stop any media before we have
                                ; call progress (meaning the SIP channel will not send 183 Session
                                ; Progress for early media). Default is "yes". Also make sure that
                                ; the SIP peer is configured with progressinband=never. 
                                ;
                                ; In order for "noanswer" applications to work, you need to run
                                ; the progress() application in the priority before the app.

;progressinband=no              ; If we should generate in-band ringing. Always
                                ; use 'never' to never use in-band signalling, even in cases
                                ; where some buggy devices might not render it
                                ; Valid values: yes, no, never Default: no
;useragent=Asterisk PBX         ; Allows you to change the user agent string
                                ; The default user agent string also contains the Asterisk
                                ; version. If you don't want to expose this, change the
                                ; useragent string.
;promiscredir = no              ; If yes, allows 302 or REDIR to non-local SIP address
                                ; Note that promiscredir when redirects are made to the
                                ; local system will cause loops since Asterisk is incapable
                                ; of performing a "hairpin" call.
;usereqphone = no               ; If yes, ";user=phone" is added to uri that contains
                                ; a valid phone number
dtmfmode = rfc2833             ; Set default dtmfmode for sending DTMF. Default: rfc2833
                                ; Other options:
                                ; info : SIP INFO messages (application/dtmf-relay)
                                ; shortinfo : SIP INFO messages (application/dtmf)
                                ; inband : Inband audio (requires 64 kbit codec -alaw, ulaw)
                                ; auto : Use rfc2833 if offered, inband otherwise

;compactheaders = yes           ; send compact sip headers.
;
;videosupport=yes               ; Turn on support for SIP video. You need to turn this
                                ; on in this section to get any video support at all.
                                ; You can turn it off on a per peer basis if the general
                                ; video support is enabled, but you can't enable it for
                                ; one peer only without enabling in the general section.
                                ; If you set videosupport to "always", then RTP ports will
                                ; always be set up for video, even on clients that don't
                                ; support it.  This assists callfile-derived calls and
                                ; certain transferred calls to use always use video when
                                ; available. [yes|NO|always]

;textsupport=no                 ; Support for ITU-T T.140 realtime text.
                                ; The default value is "no".

;maxcallbitrate=384             ; Maximum bitrate for video calls (default 384 kb/s)
                                ; Videosupport and maxcallbitrate is settable
                                ; for peers and users as well
;authfailureevents=no           ; generate manager "peerstatus" events when peer can't
                                ; authenticate with Asterisk. Peerstatus will be "rejected".
;alwaysauthreject = yes         ; When an incoming INVITE or REGISTER is to be rejected,
                                ; for any reason, always reject with an identical response
                                ; equivalent to valid username and invalid password/hash
                                ; instead of letting the requester know whether there was
                                ; a matching user or peer for their request.  This reduces
                                ; the ability of an attacker to scan for valid SIP usernames.
                                ; This option is set to "yes" by default.

;auth_options_requests = yes    ; Enabling this option will authenticate OPTIONS requests just like
                                ; INVITE requests are.  By default this option is disabled.

;accept_outofcall_message = no  ; Disable this option to reject all MESSAGE requests outside of a
                                ; call.  By default, this option is enabled.  When enabled, MESSAGE
                                ; requests are passed in to the dialplan.

;outofcall_message_context = messages ; Context all out of dialog msgs are sent to. When this
                                      ; option is not set, the context used during peer matching
                                      ; is used. This option can be defined at both the peer and
                                      ; global level.

;auth_message_requests = yes    ; Enabling this option will authenticate MESSAGE requests.
                                ; By default this option is enabled.  However, it can be disabled
                                ; should an application desire to not load the Asterisk server with
                                ; doing authentication and implement end to end security in the
                                ; message body.

;g726nonstandard = yes          ; If the peer negotiates G726-32 audio, use AAL2 packing
                                ; order instead of RFC3551 packing order (this is required
                                ; for Sipura and Grandstream ATAs, among others). This is
                                ; contrary to the RFC3551 specification, the peer _should_
                                ; be negotiating AAL2-G726-32 instead :-(
;outboundproxy=proxy.provider.domain            ; send outbound signaling to this proxy, not directly to the devices
;outboundproxy=proxy.provider.domain:8080       ; send outbound signaling to this proxy, not directly to the devices
;outboundproxy=proxy.provider.domain,force      ; Send ALL outbound signalling to proxy, ignoring route: headers
;outboundproxy=tls://proxy.provider.domain      ; same as '=proxy.provider.domain' except we try to connect with tls
;outboundproxy=192.0.2.1                        ; IPv4 address literal (default port is 5060)
;outboundproxy=2001:db8::1                      ; IPv6 address literal (default port is 5060)
;outboundproxy=192.168.0.2.1:5062               ; IPv4 address literal with explicit port
;outboundproxy=[2001:db8::1]:5062               ; IPv6 address literal with explicit port
;                                               ; (could also be tcp,udp) - defining transports on the proxy line only
;                                               ; applies for the global proxy, otherwise use the transport= option

;supportpath=yes		; This activates parsing and handling of Path header as defined in RFC 3327. This enables
				; Asterisk to route outgoing out-of-dialog requests via a set of proxies by using a pre-loaded
				; route-set defined by the Path headers in the REGISTER request.
				; NOTE: There are multiple things to consider with this setting:
				;  * As this influences routing of SIP requests make sure to not trust Path headers provided
				;    by the user's SIP client (the proxy in front of Asterisk should remove existing user
				;    provided Path headers).
				;  * When a peer has both a path and outboundproxy set, the path will be added to Route: header
				;    but routing to next hop is done using the outboundproxy.
				;  * If set globally, not only will all peers use the Path header, but outbound REGISTER
				;    requests from Asterisk will add path to the Supported header.

;rtsavepath=yes                 ; If using dynamic realtime, store the path headers

;matchexternaddrlocally = yes     ; Only substitute the externaddr or externhost setting if it matches
                                ; your localnet setting. Unless you have some sort of strange network
                                ; setup you will not need to enable this.

;dynamic_exclude_static = yes   ; Disallow all dynamic hosts from registering
                                ; as any IP address used for staticly defined
                                ; hosts.  This helps avoid the configuration
                                ; error of allowing your users to register at
                                ; the same address as a SIP provider.

;contactdeny=0.0.0.0/0.0.0.0           ; Use contactpermit and contactdeny to
;contactpermit=172.16.0.0/255.255.0.0  ; restrict at what IPs your users may
                                       ; register their phones.
;contactacl=named_acl_example          ; Use named ACLs defined in acl.conf

;rtp_engine=asterisk            ; RTP engine to use when communicating with the device

;
; If regcontext is specified, Asterisk will dynamically create and destroy a
; NoOp priority 1 extension for a given peer who registers or unregisters with
; us and have a "regexten=" configuration item.
; Multiple contexts may be specified by separating them with '&'. The
; actual extension is the 'regexten' parameter of the registering peer or its
; name if 'regexten' is not provided.  If more than one context is provided,
; the context must be specified within regexten by appending the desired
; context after '@'.  More than one regexten may be supplied if they are
; separated by '&'.  Patterns may be used in regexten.
;
;regcontext=sipregistrations
;regextenonqualify=yes          ; Default "no"
                                ; If you have qualify on and the peer becomes unreachable
                                ; this setting will enforce inactivation of the regexten
                                ; extension for the peer
;legacy_useroption_parsing=yes	; Default "no"      ; If you have this option enabled and there are semicolons
                                                    ; in the user field of a sip URI, the field be truncated
                                                    ; at the first semicolon seen. This effectively makes
                                                    ; semicolon a non-usable character for peer names, extensions,
                                                    ; and maybe other, less tested things.  This can be useful
                                                    ; for improving compatability with devices that like to use
                                                    ; user options for whatever reason.  The behavior is similar to
                                                    ; how SIP URI's were typically handled in 1.6.2, hence the name.

;send_diversion=no              ; Default "yes"     ; Asterisk normally sends Diversion headers with certain SIP
                                                    ; invites to relay data about forwarded calls. If this option
                                                    ; is disabled, Asterisk won't send Diversion headers unless
                                                    ; they are added manually.

; The shrinkcallerid function removes '(', ' ', ')', non-trailing '.', and '-' not
; in square brackets.  For example, the caller id value 555.5555 becomes 5555555
; when this option is enabled.  Disabling this option results in no modification
; of the caller id value, which is necessary when the caller id represents something
; that must be preserved.  This option can only be used in the [general] section.
; By default this option is on.
;
;shrinkcallerid=yes     ; on by default


;use_q850_reason = no ; Default "no"
                      ; Set to yes add Reason header and use Reason header if it is available.

; When the Transfer() application sends a REFER SIP message, extra headers specified in
; the dialplan by way of SIPAddHeader are sent out with that message. 1.8 and earlier did not
; add the extra headers. To revert to 1.8- behavior, call SIPRemoveHeader with no arguments
; before calling Transfer() to remove all additional headers from the channel. The setting
; below is for transitional compatibility only.
;
;refer_addheaders=yes	; on by default

;autocreatepeer=no             ; Allow any UAC not explicitly defined to register
                               ; WITHOUT AUTHENTICATION. Enabling this options poses a high
                               ; potential security risk and should be avoided unless the
                               ; server is behind a trusted firewall.
                               ; If set to "yes", then peers created in this fashion
                               ; are purged during SIP reloads.
                               ; When set to "persist", the peers created in this fashion
                               ; are not purged during SIP reloads.

;
; ----------------------- TLS settings ------------------------------------------------------------
;tlscertfile=</path/to/certificate.pem> ; Certificate chain (*.pem format only) to use for TLS connections
                                        ; The certificates must be sorted starting with the subject's certificate
                                        ; and followed by intermediate CA certificates if applicable.
                                        ; Default is to look for "asterisk.pem" in current directory

;tlsprivatekey=</path/to/private.pem> ; Private key file (*.pem format only) for TLS connections.
                                      ; If no tlsprivatekey is specified, tlscertfile is searched for
                                      ; for both public and private key.

;tlscafile=</path/to/certificate>
;        If the server your connecting to uses a self signed certificate
;        you should have their certificate installed here so the code can
;        verify the authenticity of their certificate.

;tlscapath=</path/to/ca/dir>
;        A directory full of CA certificates.  The files must be named with
;        the CA subject name hash value.
;        (see man SSL_CTX_load_verify_locations for more info)

;tlsdontverifyserver=[yes|no]
;        If set to yes, don't verify the servers certificate when acting as
;        a client.  If you don't have the server's CA certificate you can
;        set this and it will connect without requiring tlscafile to be set.
;        Default is no.

;tlscipher=<SSL cipher string>
;        A string specifying which SSL ciphers to use or not use
;        A list of valid SSL cipher strings can be found at:
;                http://www.openssl.org/docs/apps/ciphers.html#CIPHER_STRINGS
;
;tlsclientmethod=tlsv1     ; values include tlsv1, sslv3, sslv2.
                           ; Specify protocol for outbound client connections.
                           ; If left unspecified, the default is the general-
                           ; purpose version-flexible SSL/TLS method (sslv23).
                           ; With that, the actual protocol version used will
                           ; be negotiated to the highest version mutually
                           ; supported by Asterisk and the remote server, i.e.
                           ; TLSv1.2. The supported protocols are listed at
                           ; http://www.openssl.org/docs/ssl/SSL_CTX_new.html
                           ; SSLv2 and SSLv3 are disabled within Asterisk.
                           ; Your distribution might have changed that list
                           ; further.
;
; -------------------------- SIP timers ----------------------------------------------------
; These timers are used primarily in INVITE transactions.
; The default for Timer T1 is 500 ms or the measured run-trip time between
; Asterisk and the device if you have qualify=yes for the device.
;
;t1min=100                      ; Minimum roundtrip time for messages to monitored hosts
                                ; Defaults to 100 ms
;timert1=500                    ; Default T1 timer
                                ; Defaults to 500 ms or the measured round-trip
                                ; time to a peer (qualify=yes).
;timerb=32000                   ; Call setup timer. If a provisional response is not received
                                ; in this amount of time, the call will autocongest
                                ; Defaults to 64*timert1

; -------------------------- RTP timers ----------------------------------------------------
; These timers are currently used for both audio and video streams. The RTP timeouts
; are only applied to the audio channel.
; The settings are settable in the global section as well as per device
;
;rtptimeout=60                  ; Terminate call if 60 seconds of no RTP or RTCP activity
                                ; on the audio channel
                                ; when we're not on hold. This is to be able to hangup
                                ; a call in the case of a phone disappearing from the net,
                                ; like a powerloss or grandma tripping over a cable.
;rtpholdtimeout=300             ; Terminate call if 300 seconds of no RTP or RTCP activity
                                ; on the audio channel
                                ; when we're on hold (must be > rtptimeout)
;rtpkeepalive=<secs>            ; Send keepalives in the RTP stream to keep NAT open
                                ; (default is off - zero)

; -------------------------- SIP Session-Timers (RFC 4028)------------------------------------
; SIP Session-Timers provide an end-to-end keep-alive mechanism for active SIP sessions.
; This mechanism can detect and reclaim SIP channels that do not terminate through normal
; signaling procedures. Session-Timers can be configured globally or at a user/peer level.
; The operation of Session-Timers is driven by the following configuration parameters:
;
; * session-timers    - Session-Timers feature operates in the following three modes:
;                            originate : Request and run session-timers always
;                            accept    : Run session-timers only when requested by other UA
;                            refuse    : Do not run session timers in any case
;                       The default mode of operation is 'accept'.
; * session-expires   - Maximum session refresh interval in seconds. Defaults to 1800 secs.
; * session-minse     - Minimum session refresh interval in seconds. Defualts to 90 secs.
; * session-refresher - The session refresher (uac|uas). Defaults to 'uas'.
;                            uac - Default to the caller initially refreshing when possible
;                            uas - Default to the callee initially refreshing when possible
;
; Note that, due to recommendations in RFC 4028, Asterisk will always honor the other
; endpoint's preference for who will handle refreshes. Asterisk will never override the
; preferences of the other endpoint. Doing so could result in Asterisk and the endpoint
; fighting over who sends the refreshes. This holds true for the initiation of session
; timers and subsequent re-INVITE requests whether Asterisk is the caller or callee, or
; whether Asterisk is currently the refresher or not.
;
;session-timers=originate
;session-expires=600
;session-minse=90
;session-refresher=uac
;
; -------------------------- SIP DEBUGGING ---------------------------------------------------
;sipdebug = yes                 ; Turn on SIP debugging by default, from
                                ; the moment the channel loads this configuration.
                                ; NOTE: You cannot use the CLI to turn it off. You'll
                                ; need to edit this and reload the config.
;recordhistory=yes              ; Record SIP history by default
                                ; (see sip history / sip no history)
;dumphistory=yes                ; Dump SIP history at end of SIP dialogue
                                ; SIP history is output to the DEBUG logging channel


; -------------------------- STATUS NOTIFICATIONS (SUBSCRIPTIONS) ----------------------------
; You can subscribe to the status of extensions with a "hint" priority
; (See extensions.conf.sample for examples)
; chan_sip support two major formats for notifications: dialog-info and SIMPLE
;
; You will get more detailed reports (busy etc) if you have a call counter enabled
; for a device.
;
; If you set the busylevel, we will indicate busy when we have a number of calls that
; matches the busylevel treshold.
;
; For queues, you will need this level of detail in status reporting, regardless
; if you use SIP subscriptions. Queues and manager use the same internal interface
; for reading status information.
;
; Note: Subscriptions does not work if you have a realtime dialplan and use the
; realtime switch.
;
;allowsubscribe=no              ; Disable support for subscriptions. (Default is yes)
;subscribecontext = default     ; Set a specific context for SUBSCRIBE requests
                                ; Useful to limit subscriptions to local extensions
                                ; Settable per peer/user also
;notifyringing = no             ; Control whether subscriptions already INUSE get sent
                                ; RINGING when another call is sent (default: yes)
;notifyhold = yes               ; Notify subscriptions on HOLD state (default: no)
                                ; Turning on notifyringing and notifyhold will add a lot
                                ; more database transactions if you are using realtime.
;notifycid = yes                ; Control whether caller ID information is sent along with
                                ; dialog-info+xml notifications (supported by snom phones).
                                ; Note that this feature will only work properly when the
                                ; incoming call is using the same extension and context that
                                ; is being used as the hint for the called extension.  This means
                                ; that it won't work when using subscribecontext for your sip
                                ; user or peer (if subscribecontext is different than context).
                                ; This is also limited to a single caller, meaning that if an
                                ; extension is ringing because multiple calls are incoming,
                                ; only one will be used as the source of caller ID.  Specify
                                ; 'ignore-context' to ignore the called context when looking
                                ; for the caller's channel.  The default value is 'no.' Setting
                                ; notifycid to 'ignore-context' also causes call-pickups attempted
                                ; via SNOM's NOTIFY mechanism to set the context for the call pickup
                                ; to PICKUPMARK.
;callcounter = yes              ; Enable call counters on devices. This can be set per
                                ; device too.

; ---------------------------------------- T.38 FAX SUPPORT ----------------------------------
;
; This setting is available in the [general] section as well as in device configurations.
; Setting this to yes enables T.38 FAX (UDPTL) on SIP calls; it defaults to off.
;
; t38pt_udptl = yes            ; Enables T.38 with FEC error correction.
; t38pt_udptl = yes,fec        ; Enables T.38 with FEC error correction.
; t38pt_udptl = yes,redundancy ; Enables T.38 with redundancy error correction.
; t38pt_udptl = yes,none       ; Enables T.38 with no error correction.
;
; In some cases, T.38 endpoints will provide a T38FaxMaxDatagram value (during T.38 setup) that
; is based on an incorrect interpretation of the T.38 recommendation, and results in failures
; because Asterisk does not believe it can send T.38 packets of a reasonable size to that
; endpoint (Cisco media gateways are one example of this situation). In these cases, during a
; T.38 call you will see warning messages on the console/in the logs from the Asterisk UDPTL
; stack complaining about lack of buffer space to send T.38 FAX packets. If this occurs, you
; can set an override (globally, or on a per-device basis) to make Asterisk ignore the
; T38FaxMaxDatagram value specified by the other endpoint, and use a configured value instead.
; This can be done by appending 'maxdatagram=<value>' to the t38pt_udptl configuration option,
; like this:
;
; t38pt_udptl = yes,fec,maxdatagram=400 ; Enables T.38 with FEC error correction and overrides
;                                       ; the other endpoint's provided value to assume we can
;                                       ; send 400 byte T.38 FAX packets to it.
;
; FAX detection will cause the SIP channel to jump to the 'fax' extension (if it exists)
; based one or more events being detected. The events that can be detected are an incoming
; CNG tone or an incoming T.38 re-INVITE request.
;
; faxdetect = yes		; Default 'no', 'yes' enables both CNG and T.38 detection
; faxdetect = cng		; Enables only CNG detection
; faxdetect = t38		; Enables only T.38 detection
;
; ---------------------------------------- OUTBOUND SIP REGISTRATIONS  ------------------------
; Asterisk can register as a SIP user agent to a SIP proxy (provider)
; Format for the register statement is:
;       register => [peer?][transport://]user[@domain][:secret[:authuser]]@host[:port][/extension][~expiry]
;
;
;
; domain is either
;	- domain in DNS
; 	- host name in DNS
;	- the name of a peer defined below or in realtime
; The domain is where you register your username, so your SIP uri you are registering to
; is username@domain
;
; If no extension is given, the 's' extension is used. The extension needs to
; be defined in extensions.conf to be able to accept calls from this SIP proxy
; (provider).
;
; A similar effect can be achieved by adding a "callbackextension" option in a peer section.
; this is equivalent to having the following line in the general section:
;
;        register => username:secret@host/callbackextension
;
; and more readable because you don't have to write the parameters in two places
; (note that the "port" is ignored - this is a bug that should be fixed).
;
; Note that a register= line doesn't mean that we will match the incoming call in any
; other way than described above. If you want to control where the call enters your
; dialplan, which context, you want to define a peer with the hostname of the provider's
; server. If the provider has multiple servers to place calls to your system, you need
; a peer for each server.
;
; Beginning with Asterisk version 1.6.2, the "user" portion of the register line may
; contain a port number. Since the logical separator between a host and port number is a
; ':' character, and this character is already used to separate between the optional "secret"
; and "authuser" portions of the line, there is a bit of a hoop to jump through if you wish
; to use a port here. That is, you must explicitly provide a "secret" and "authuser" even if
; they are blank. See the third example below for an illustration.
;
;
; Examples:
;
;register => 1234:password@mysipprovider.com
;
;     This will pass incoming calls to the 's' extension
;
;
;register => 2345:password@sip_proxy/1234
;
;    Register 2345 at sip provider 'sip_proxy'.  Calls from this provider
;    connect to local extension 1234 in extensions.conf, default context,
;    unless you configure a [sip_proxy] section below, and configure a
;    context.
;    Tip 1: Avoid assigning hostname to a sip.conf section like [provider.com]
;    Tip 2: Use separate inbound and outbound sections for SIP providers
;           (instead of type=friend) if you have calls in both directions
;
;register => 3456@mydomain:5082::@mysipprovider.com
;
;    Note that in this example, the optional authuser and secret portions have
;    been left blank because we have specified a port in the user section
;
;register => tls://username:xxxxxx@sip-tls-proxy.example.org
;
;    The 'transport' part defaults to 'udp' but may also be 'tcp' or 'tls'.
;    Using 'udp://' explicitly is also useful in case the username part
;    contains a '/' ('user/name').

;registertimeout=20             ; retry registration calls every 20 seconds (default)
;registerattempts=10            ; Number of registration attempts before we give up
                                ; 0 = continue forever, hammering the other server
                                ; until it accepts the registration
                                ; Default is 0 tries, continue forever
;register_retry_403=yes         ; Treat 403 responses to registrations as if they were
                                ; 401 responses and continue retrying according to normal
                                ; retry rules.

; ---------------------------------------- OUTBOUND MWI SUBSCRIPTIONS -------------------------
; Asterisk can subscribe to receive the MWI from another SIP server and store it locally for retrieval
; by other phones. At this time, you can only subscribe using UDP as the transport.
; Format for the mwi register statement is:
;       mwi => user[:secret[:authuser]]@host[:port]/mailbox
;
; Examples:
;mwi => 1234:password@mysipprovider.com/1234
;mwi => 1234:password@myportprovider.com:6969/1234
;mwi => 1234:password:authuser@myauthprovider.com/1234
;mwi => 1234:password:authuser@myauthportprovider.com:6969/1234
;
; MWI received will be stored in the 1234 mailbox of the SIP_Remote context.
; It can be used by other phones by following the below:
; mailbox=1234@SIP_Remote
; ---------------------------------------- NAT SUPPORT ------------------------
;
; WARNING: SIP operation behind a NAT is tricky and you really need
; to read and understand well the following section.
;
; When Asterisk is behind a NAT device, the "local" address (and port) that
; a socket is bound to has different values when seen from the inside or
; from the outside of the NATted network. Unfortunately this address must
; be communicated to the outside (e.g. in SIP and SDP messages), and in
; order to determine the correct value Asterisk needs to know:
;
; + whether it is talking to someone "inside" or "outside" of the NATted network.
;   This is configured by assigning the "localnet" parameter with a list
;   of network addresses that are considered "inside" of the NATted network.
;   IF LOCALNET IS NOT SET, THE EXTERNAL ADDRESS WILL NOT BE SET CORRECTLY.
;   Multiple entries are allowed, e.g. a reasonable set is the following:
;
;      localnet=192.168.0.0/255.255.0.0 ; RFC 1918 addresses
;      localnet=10.0.0.0/255.0.0.0      ; Also RFC1918
;      localnet=172.16.0.0/12           ; Another RFC1918 with CIDR notation
;      localnet=169.254.0.0/255.255.0.0 ; Zero conf local network
;
; + the "externally visible" address and port number to be used when talking
;   to a host outside the NAT. This information is derived by one of the
;   following (mutually exclusive) config file parameters:
;
;   a. "externaddr = hostname[:port]" specifies a static address[:port] to
;      be used in SIP and SDP messages.
;      The hostname is looked up only once, when [re]loading sip.conf .
;      If a port number is not present, use the port specified in the "udpbindaddr"
;      (which is not guaranteed to work correctly, because a NAT box might remap the
;      port number as well as the address).
;      This approach can be useful if you have a NAT device where you can
;      configure the mapping statically. Examples:
;
;        externaddr = 12.34.56.78          ; use this address.
;        externaddr = 12.34.56.78:9900     ; use this address and port.
;        externaddr = mynat.my.org:12600   ; Public address of my nat box.
;        externtcpport = 9900   ; The externally mapped tcp port, when Asterisk is behind a static NAT or PAT. 
;                               ; externtcpport will default to the externaddr or externhost port if either one is set. 
;        externtlsport = 12600  ; The externally mapped tls port, when Asterisk is behind a static NAT or PAT.
;                               ; externtlsport port will default to the RFC designated port of 5061.	
;
;   b. "externhost = hostname[:port]" is similar to "externaddr" except
;      that the hostname is looked up every "externrefresh" seconds
;      (default 10s). This can be useful when your NAT device lets you choose
;      the port mapping, but the IP address is dynamic.
;      Beware, you might suffer from service disruption when the name server
;      resolution fails. Examples:
;
;        externhost=foo.dyndns.net       ; refreshed periodically
;        externrefresh=180               ; change the refresh interval
;
;   Note that at the moment all these mechanism work only for the SIP socket.
;   The IP address discovered with externaddr/externhost is reused for
;   media sessions as well, but the port numbers are not remapped so you
;   may still experience problems.
;
; NOTE 1: in some cases, NAT boxes will use different port numbers in
; the internal<->external mapping. In these cases, the "externaddr" and
; "externhost" might not help you configure addresses properly.
;
; NOTE 2: when using "externaddr" or "externhost", the address part is
; also used as the external address for media sessions. Thus, the port
; information in the SDP may be wrong!
;
; In addition to the above, Asterisk has an additional "nat" parameter to
; address NAT-related issues in incoming SIP or media sessions.
; In particular, depending on the 'nat= ' settings described below, Asterisk
; may override the address/port information specified in the SIP/SDP messages,
; and use the information (sender address) supplied by the network stack instead.
; However, this is only useful if the external traffic can reach us.
; The following settings are allowed (both globally and in individual sections):
;
;   nat = no                ; Do no special NAT handling other than RFC3581
;   nat = force_rport       ; Pretend there was an rport parameter even if there wasn't
;   nat = comedia           ; Send media to the port Asterisk received it from regardless
;                           ; of where the SDP says to send it.
;   nat = auto_force_rport  ; Set the force_rport option if Asterisk detects NAT (default)
;   nat = auto_comedia      ; Set the comedia option if Asterisk detects NAT
;
; The nat settings can be combined. For example, to set both force_rport and comedia
; one would set nat=force_rport,comedia. If any of the comma-separated options is 'no',
; Asterisk will ignore any other settings and set nat=no. If one of the "auto" settings
; is used in conjunction with its non-auto counterpart (nat=comedia,auto_comedia), then
; the non-auto option will be ignored.
;
; The RFC 3581-defined 'rport' parameter allows a client to request that Asterisk send
; SIP responses to it via the source IP and port from which the request originated
; instead of the address/port listed in the top-most Via header. This is useful if a
; client knows that it is behind a NAT and therefore cannot guess from what address/port
; its request will be sent. Asterisk will always honor the 'rport' parameter if it is
; sent. The force_rport setting causes Asterisk to always send responses back to the
; address/port from which it received requests; even if the other side doesn't support
; adding the 'rport' parameter.
;
; 'comedia RTP handling' refers to the technique of sending RTP to the port that the
; the other endpoint's RTP arrived from, and means 'connection-oriented media'. This is
; only partially related to RFC 4145 which was referred to as COMEDIA while it was in
; draft form. This method is used to accomodate endpoints that may be located behind
; NAT devices, and as such the address/port they tell Asterisk to send RTP packets to
; for their media streams is not the actual address/port that will be used on the nearer
; side of the NAT.
;
; IT IS IMPORTANT TO NOTE that if the nat setting in the general section differs from
; the nat setting in a peer definition, then the peer username will be discoverable
; by outside parties as Asterisk will respond to different ports for defined and
; undefined peers. For this reason it is recommended to ONLY DEFINE NAT SETTINGS IN THE
; GENERAL SECTION. Specifically, if nat=force_rport in one section and nat=no in the
; other, then valid peers with settings differing from those in the general section will
; be discoverable.
;
; In addition to these settings, Asterisk *always* uses 'symmetric RTP' mode as defined by
; RFC 4961; Asterisk will always send RTP packets from the same port number it expects
; to receive them on.
;
; The IP address used for media (audio, video, and text) in the SDP can also be overridden by using
; the media_address configuration option. This is only applicable to the general section and
; can not be set per-user or per-peer.
;
; media_address = 172.16.42.1
;
; Through the use of the res_stun_monitor module, Asterisk has the ability to detect when the
; perceived external network address has changed.  When the stun_monitor is installed and
; configured, chan_sip will renew all outbound registrations when the monitor detects any sort
; of network change has occurred. By default this option is enabled, but only takes effect once
; res_stun_monitor is configured.  If res_stun_monitor is enabled and you wish to not
; generate all outbound registrations on a network change, use the option below to disable
; this feature.
;
; subscribe_network_change_event = yes ; on by default
;
; ICE/STUN/TURN usage can be enabled globally or on a per-peer basis using the icesupport
; configuration option. When set to yes ICE support is enabled. When set to no it is disabled.
; It is disabled by default.
;
; icesupport = yes

; ---------------------------------- MEDIA HANDLING --------------------------------
; By default, Asterisk tries to re-invite media streams to an optimal path. If there's
; no reason for Asterisk to stay in the media path, the media will be redirected.
; This does not really work well in the case where Asterisk is outside and the
; clients are on the inside of a NAT. In that case, you want to set directmedia=nonat.
;
;directmedia=yes                ; Asterisk by default tries to redirect the
                                ; RTP media stream to go directly from
                                ; the caller to the callee.  Some devices do not
                                ; support this (especially if one of them is behind a NAT).
                                ; The default setting is YES. If you have all clients
                                ; behind a NAT, or for some other reason want Asterisk to
                                ; stay in the audio path, you may want to turn this off.

                                ; This setting also affect direct RTP
                                ; at call setup (a new feature in 1.4 - setting up the
                                ; call directly between the endpoints instead of sending
                                ; a re-INVITE).

                                ; Additionally this option does not disable all reINVITE operations.
                                ; It only controls Asterisk generating reINVITEs for the specific
                                ; purpose of setting up a direct media path. If a reINVITE is
                                ; needed to switch a media stream to inactive (when placed on
                                ; hold) or to T.38, it will still be done, regardless of this 
                                ; setting. Note that direct T.38 is not supported.

;directmedia=nonat              ; An additional option is to allow media path redirection
                                ; (reinvite) but only when the peer where the media is being
                                ; sent is known to not be behind a NAT (as the RTP core can
                                ; determine it based on the apparent IP address the media
                                ; arrives from).

;directmedia=update             ; Yet a third option... use UPDATE for media path redirection,
                                ; instead of INVITE. This can be combined with 'nonat', as
                                ; 'directmedia=update,nonat'. It implies 'yes'.

;directmedia=outgoing           ; When sending directmedia reinvites, do not send an immediate
                                ; reinvite on an incoming call leg. This option is useful when
                                ; peered with another SIP user agent that is known to send
                                ; immediate direct media reinvites upon call establishment. Setting
                                ; the option in this situation helps to prevent potential glares.
                                ; Setting this option implies 'yes'.

;directrtpsetup=yes             ; Enable the new experimental direct RTP setup. This sets up
                                ; the call directly with media peer-2-peer without re-invites.
                                ; Will not work for video and cases where the callee sends
                                ; RTP payloads and fmtp headers in the 200 OK that does not match the
                                ; callers INVITE. This will also fail if directmedia is enabled when
                                ; the device is actually behind NAT.

;directmediadeny=0.0.0.0/0      ; Use directmediapermit and directmediadeny to restrict 
;directmediapermit=172.16.0.0/16; which peers should be able to pass directmedia to each other
                                ; (There is no default setting, this is just an example)
                                ; Use this if some of your phones are on IP addresses that
                                ; can not reach each other directly. This way you can force 
                                ; RTP to always flow through asterisk in such cases.
;directmediaacl=acl_example     ; Use named ACLs defined in acl.conf

;ignoresdpversion=yes           ; By default, Asterisk will honor the session version
                                ; number in SDP packets and will only modify the SDP
                                ; session if the version number changes. This option will
                                ; force asterisk to ignore the SDP session version number
                                ; and treat all SDP data as new data.  This is required
                                ; for devices that send us non standard SDP packets
                                ; (observed with Microsoft OCS). By default this option is
                                ; off.

;sdpsession=Asterisk PBX        ; Allows you to change the SDP session name string, (s=)
                                ; Like the useragent parameter, the default user agent string
                                ; also contains the Asterisk version.
;sdpowner=root                  ; Allows you to change the username field in the SDP owner string, (o=)
                                ; This field MUST NOT contain spaces
;encryption=no                  ; Whether to offer SRTP encrypted media (and only SRTP encrypted media)
                                ; on outgoing calls to a peer. Calls will fail with HANGUPCAUSE=58 if
                                ; the peer does not support SRTP. Defaults to no.
;encryption_taglen=80           ; Set the auth tag length offered in the INVITE either 32/80 default 80
;
;avpf=yes                       ; Enable inter-operability with media streams using the AVPF RTP profile.
				; This will cause all offers and answers to use AVPF (or SAVPF). This
				; option may be specified at the global or peer scope.
;force_avp=yes			; Force 'RTP/AVP', 'RTP/AVPF', 'RTP/SAVP', and 'RTP/SAVPF' to be used for
				; media streams when appropriate, even if a DTLS stream is present.
;rtcp_mux=yes			; Enable support for RFC 5761 RTCP multiplexing which is required for
				; WebRTC support
; ---------------------------------------- REALTIME SUPPORT ------------------------
; For additional information on ARA, the Asterisk Realtime Architecture,
; please read https://wiki.asterisk.org/wiki/display/AST/Realtime+Database+Configuration
;
;rtcachefriends=yes             ; Cache realtime friends by adding them to the internal list
                                ; just like friends added from the config file only on a
                                ; as-needed basis? (yes|no)

;rtsavesysname=yes              ; Save systemname in realtime database at registration
                                ; Default= no

;rtupdate=yes                   ; Send registry updates to database using realtime? (yes|no)
                                ; If set to yes, when a SIP UA registers successfully, the ip address,
                                ; the origination port, the registration period, and the username of
                                ; the UA will be set to database via realtime.
                                ; If not present, defaults to 'yes'. Note: realtime peers will
                                ; probably not function across reloads in the way that you expect, if
                                ; you turn this option off.
;rtautoclear=yes                ; Auto-Expire friends created on the fly on the same schedule
                                ; as if it had just registered? (yes|no|<seconds>)
                                ; If set to yes, when the registration expires, the friend will
                                ; vanish from the configuration until requested again. If set
                                ; to an integer, friends expire within this number of seconds
                                ; instead of the registration interval.

;ignoreregexpire=yes            ; Enabling this setting has two functions:
                                ;
                                ; For non-realtime peers, when their registration expires, the
                                ; information will _not_ be removed from memory or the Asterisk database
                                ; if you attempt to place a call to the peer, the existing information
                                ; will be used in spite of it having expired
                                ;
                                ; For realtime peers, when the peer is retrieved from realtime storage,
                                ; the registration information will be used regardless of whether
                                ; it has expired or not; if it expires while the realtime peer
                                ; is still in memory (due to caching or other reasons), the
                                ; information will not be removed from realtime storage

; ---------------------------------------- SIP DOMAIN SUPPORT ------------------------
; Incoming INVITE and REFER messages can be matched against a list of 'allowed'
; domains, each of which can direct the call to a specific context if desired.
; By default, all domains are accepted and sent to the default context or the
; context associated with the user/peer placing the call.
; REGISTER to non-local domains will be automatically denied if a domain
; list is configured.
;
; Domains can be specified using:
; domain=<domain>[,<context>]
; Examples:
; domain=myasterisk.dom
; domain=customer.com,customer-context
;
; In addition, all the 'default' domains associated with a server should be
; added if incoming request filtering is desired.
; autodomain=yes
;
; To disallow requests for domains not serviced by this server:
; allowexternaldomains=no

;domain=mydomain.tld,mydomain-incoming
                                ; Add domain and configure incoming context
                                ; for external calls to this domain
;domain=1.2.3.4                 ; Add IP address as local domain
                                ; You can have several "domain" settings
;allowexternaldomains=no        ; Disable INVITE and REFER to non-local domains
                                ; Default is yes
;autodomain=yes                 ; Turn this on to have Asterisk add local host
                                ; name and local IP to domain list.

; fromdomain=mydomain.tld       ; When making outbound SIP INVITEs to
                                ; non-peers, use your primary domain "identity"
                                ; for From: headers instead of just your IP
                                ; address. This is to be polite and
                                ; it may be a mandatory requirement for some
                                ; destinations which do not have a prior
                                ; account relationship with your server.

; ----------------------------- Advice of Charge CONFIGURATION --------------------------
; snom_aoc_enabled = yes;     ; This options turns on and off support for sending AOC-D and
                              ; AOC-E to snom endpoints.  This option can be used both in the
                              ; peer and global scope.  The default for this option is off.


; ----------------------------- JITTER BUFFER CONFIGURATION --------------------------
; jbenable = yes              ; Enables the use of a jitterbuffer on the receiving side of a
                              ; SIP channel. Defaults to "no". An enabled jitterbuffer will
                              ; be used only if the sending side can create and the receiving
                              ; side can not accept jitter. The SIP channel can accept jitter,
                              ; thus a jitterbuffer on the receive SIP side will be used only
                              ; if it is forced and enabled.

; jbforce = no                ; Forces the use of a jitterbuffer on the receive side of a SIP
                              ; channel. Defaults to "no".

; jbmaxsize = 200             ; Max length of the jitterbuffer in milliseconds.

; jbresyncthreshold = 1000    ; Jump in the frame timestamps over which the jitterbuffer is
                              ; resynchronized. Useful to improve the quality of the voice, with
                              ; big jumps in/broken timestamps, usually sent from exotic devices
                              ; and programs. Defaults to 1000.

; jbimpl = fixed              ; Jitterbuffer implementation, used on the receiving side of a SIP
                              ; channel. Two implementations are currently available - "fixed"
                              ; (with size always equals to jbmaxsize) and "adaptive" (with
                              ; variable size, actually the new jb of IAX2). Defaults to fixed.

; jbtargetextra = 40          ; This option only affects the jb when 'jbimpl = adaptive' is set.
                              ; The option represents the number of milliseconds by which the new jitter buffer
                              ; will pad its size. the default is 40, so without modification, the new
                              ; jitter buffer will set its size to the jitter value plus 40 milliseconds.
                              ; increasing this value may help if your network normally has low jitter,
                              ; but occasionally has spikes.

; jblog = no                  ; Enables jitterbuffer frame logging. Defaults to "no".

; ----------------------------------------------------------------------------------

[authentication]
; Global credentials for outbound calls, i.e. when a proxy challenges your
; Asterisk server for authentication. These credentials override
; any credentials in peer/register definition if realm is matched.
;
; This way, Asterisk can authenticate for outbound calls to other
; realms. We match realm on the proxy challenge and pick an set of
; credentials from this list
; Syntax:
;        auth = <user>:<secret>@<realm>
;        auth = <user>#<md5secret>@<realm>
; Example:
;auth=mark:topsecret@digium.com
;
; You may also add auth= statements to [peer] definitions
; Peer auth= override all other authentication settings if we match on realm

; -----------------------------------------------------------------------------
; DEVICE CONFIGURATION
;
; SIP entities have a 'type' which determines their roles within Asterisk.
; * For entities with 'type=peer':
;   Peers handle both inbound and outbound calls and are matched by ip/port, so for
;   The case of incoming calls from the peer, the IP address must match in order for
;   The invitation to work. This means calls made from either direction won't work if
;   The peer is unregistered while host=dynamic or if the host is otherise not set to
;   the correct IP of the sender.
; * For entities with 'type=user':
;   Asterisk users handle inbound calls only (meaning they call Asterisk, Asterisk can't
;   call them) and are matched by their authorization information (authname and secret).
;   Asterisk doesn't rely on their IP and will accept calls regardless of the host setting
;   as long as the incoming SIP invite authorizes successfully.
; * For entities with 'type=friend':
;   Asterisk will create the entity as both a friend and a peer. Asterisk will accept
;   calls from friends like it would for users, requiring only that the authorization
;   matches rather than the IP address. Since it is also a peer, a friend entity can
;   be called as long as its IP is known to Asterisk. In the case of host=dynamic,
;   this means it is necessary for the entity to register before Asterisk can call it.
; 
; Use remotesecret for outbound authentication, and secret for authenticating
; inbound requests. For historical reasons, if no remotesecret is supplied for an
; outbound registration or call, the secret will be used. 
;
; For device names, we recommend using only a-z, numerics (0-9) and underscore
;
; For local phones, type=friend works most of the time
;
; If you have one-way audio, you probably have NAT problems.
; If Asterisk is on a public IP, and the phone is inside of a NAT device
; you will need to configure nat option for those phones.
; Also, turn on qualify=yes to keep the nat session open
;
; Configuration options available
; --------------------
; context
; callingpres
; permit
; deny
; secret
; md5secret
; remotesecret
; transport
; dtmfmode
; directmedia
; nat
; callgroup
; pickupgroup
; language
; allow
; disallow
; autoframing
; insecure
; trustrpid
; trust_id_outbound
; progressinband
; promiscredir
; useclientcode
; accountcode
; setvar
; callerid
; amaflags
; callcounter
; busylevel
; allowoverlap
; allowsubscribe
; allowtransfer
; ignoresdpversion
; subscribecontext
; template
; videosupport
; maxcallbitrate
; rfc2833compensate
; Note: app_voicemail mailboxes must be in the form of mailbox@context.
; mailbox
; session-timers
; session-expires
; session-minse
; session-refresher
; t38pt_usertpsource
; regexten
; fromdomain
; fromuser
; host
; port
; qualify
; keepalive
; defaultip
; defaultuser
; rtptimeout
; rtpholdtimeout
; sendrpid
; outboundproxy
; rfc2833compensate
; callbackextension
; timert1
; timerb
; qualifyfreq
; t38pt_usertpsource
; contactpermit         ; Limit what a host may register as (a neat trick
; contactdeny           ; is to register at the same IP as a SIP provider,
; contactacl            ; then call oneself, and get redirected to that
;                       ; same location).
; directmediapermit
; directmediadeny
; directmediaacl
; unsolicited_mailbox
; use_q850_reason
; maxforwards
; encryption
; description		; Used to provide a description of the peer in console output
; dtlsenable
; dtlsverify
; dtlsrekey
; dtlscertfile
; dtlsprivatekey
; dtlscipher
; dtlscafile
; dtlscapath
; dtlssetup
; dtlsfingerprint
; ignore_requested_pref ; Ignore the requested codec and determine the preferred codec
;						; from the peer's configuration.
;

; -----------------------------------------------------------------------------
; DTLS-SRTP CONFIGURATION
;
; DTLS-SRTP support is available if the underlying RTP engine in use supports it.
;
; Note: DTLS configuration must be set directly on a user, peer, or friend. Setting these
;       options globally in the [general] section will have no effect.
;
; dtlsenable = yes                   ; Enable or disable DTLS-SRTP support
; dtlsverify = yes                   ; Verify that provided peer certificate and fingerprint are valid
;				     ; A value of 'yes' will perform both certificate and fingerprint verification
;				     ; A value of 'no' will perform no certificate or fingerprint verification
;				     ; A value of 'fingerprint' will perform ONLY fingerprint verification
;				     ; A value of 'certificate' will perform ONLY certficiate verification
; dtlsrekey = 60                     ; Interval at which to renegotiate the TLS session and rekey the SRTP session
;                                    ; If this is not set or the value provided is 0 rekeying will be disabled
; dtlscertfile = file                ; Path to certificate file to present
; dtlsprivatekey = file              ; Path to private key for certificate file
; dtlscipher = <SSL cipher string>   ; Cipher to use for TLS negotiation
;                                    ; A list of valid SSL cipher strings can be found at:
;                                    ; http://www.openssl.org/docs/apps/ciphers.html#CIPHER_STRINGS
; dtlscafile = file                  ; Path to certificate authority certificate
; dtlscapath = path                  ; Path to a directory containing certificate authority certificates
; dtlssetup = actpass                ; Whether we are willing to accept connections, connect to the other party, or both.
;                                    ; Valid options are active (we want to connect to the other party), passive (we want to
;                                    ; accept connections only), and actpass (we will do both). This value will be used in
;                                    ; the outgoing SDP when offering and for incoming SDP offers when the remote party sends
;                                    ; actpass
; dtlsfingerprint = sha-1            ; The hash to use for the fingerprint in SDP (valid options are sha-1 and sha-256)

;[sip_proxy]
; For incoming calls only. Example: FWD (Free World Dialup)
; We match on IP address of the proxy for incoming calls
; since we can not match on username (caller id)
;type=peer
;context=from-fwd
;host=fwd.pulver.com

;[sip_proxy-out]
;type=peer                        ; we only want to call out, not be called
;remotesecret=guessit             ; Our password to their service
;defaultuser=yourusername         ; Authentication user for outbound proxies
;fromuser=yourusername            ; Many SIP providers require this!
;fromdomain=provider.sip.domain
;host=box.provider.com
;transport=udp,tcp                ; This sets the default transport type to udp for outgoing, and will
;                                 ; accept both tcp and udp. The default transport type is only used for
;                                 ; outbound messages until a Registration takes place.  During the
;                                 ; peer Registration the transport type may change to another supported
;                                 ; type if the peer requests so.

;usereqphone=yes                  ; This provider requires ";user=phone" on URI
;callcounter=yes                  ; Enable call counter
;busylevel=2                      ; Signal busy at 2 or more calls
;outboundproxy=proxy.provider.domain  ; send outbound signaling to this proxy, not directly to the peer
;port=80                          ; The port number we want to connect to on the remote side
                                  ; Also used as "defaultport" in combination with "defaultip" settings

; -- sample definition for a provider
;[provider1]
;type=peer
;host=sip.provider1.com
;fromuser=4015552299              ; how your provider knows you
;remotesecret=youwillneverguessit ; The password we use to authenticate to them
;secret=gissadetdu                ; The password they use to contact us
;callbackextension=123            ; Register with this server and require calls coming back to this extension
;transport=udp,tcp                ; This sets the transport type to udp for outgoing, and will
;                                 ;   accept both tcp and udp. Default is udp. The first transport
;                                 ;   listed will always be used for outgoing connections.
;unsolicited_mailbox=4015552299   ; If the remote SIP server sends an unsolicited MWI NOTIFY message the new/old
;                                 ;   message count will be stored in the configured virtual mailbox. It can be used
;                                 ;   by any device supporting MWI by specifying <configured value>@SIP_Remote as the
;                                 ;   mailbox.

;
; Because you might have a large number of similar sections, it is generally
; convenient to use templates for the common parameters, and add them
; the the various sections. Examples are below, and we can even leave
; the templates uncommented as they will not harm:

[basic-options](!)                ; a template
        dtmfmode=rfc2833
        context=from-office
        type=friend

[natted-phone](!,basic-options)   ; another template inheriting basic-options
        directmedia=no
        host=dynamic

[public-phone](!,basic-options)   ; another template inheriting basic-options
        directmedia=yes

[my-codecs](!)                    ; a template for my preferred codecs
        disallow=all
        allow=ilbc
        allow=g729
        allow=gsm
        allow=g723
        allow=ulaw
        ; Or, more simply:
        ;allow=!all,ilbc,g729,gsm,g723,ulaw

[ulaw-phone](!)                   ; and another one for ulaw-only
        disallow=all
        allow=ulaw
        ; Again, more simply:
        ;allow=!all,ulaw

; and finally instantiate a few phones
;
; [2133](natted-phone,my-codecs)
;        secret = peekaboo
; [2134](natted-phone,ulaw-phone)
;        secret = not_very_secret
; [2136](public-phone,ulaw-phone)
;        secret = not_very_secret_either
; ...
;

; Standard configurations not using templates look like this:
;
;[grandstream1]
;type=friend
;context=from-sip                ; Where to start in the dialplan when this phone calls
;recordonfeature=dynamicfeature1 ; Feature to use when INFO with Record: on is received.
;recordofffeature=dynamicfeature2 ; Feature to use when INFO with Record: off is received.
;callerid=John Doe <1234>        ; Full caller ID, to override the phones config
                                 ; on incoming calls to Asterisk
;description=Courtesy Phone      ; Description of the peer. Shown when doing 'sip show peers'.
;host=192.168.0.23               ; we have a static but private IP address
                                 ; No registration allowed
;directmedia=yes                 ; allow RTP voice traffic to bypass Asterisk
;dtmfmode=info                   ; either RFC2833 or INFO for the BudgeTone
;call-limit=1                    ; permit only 1 outgoing call and 1 incoming call at a time
                                 ; from the phone to asterisk (deprecated)
                                 ; 1 for the explicit peer, 1 for the explicit user,
                                 ; remember that a friend equals 1 peer and 1 user in
                                 ; memory
                                 ; There is no combined call counter for a "friend"
                                 ; so there's currently no way in sip.conf to limit
                                 ; to one inbound or outbound call per phone. Use
                                 ; the group counters in the dial plan for that.
                                 ;
;mailbox=1234@default            ; mailbox 1234 in voicemail context "default"
;disallow=all                    ; need to disallow=all before we can use allow=
;allow=ulaw                      ; Note: In user sections the order of codecs
                                 ; listed with allow= does NOT matter!
;allow=alaw
;allow=g723.1                    ; Asterisk only supports g723.1 pass-thru!
;allow=g729                      ; Pass-thru only unless g729 license obtained
;callingpres=allowed_passed_screen ; Set caller ID presentation
                                 ; See function CALLERPRES documentation for possible
                                 ; values.

;[xlite1]
; Turn off silence suppression in X-Lite ("Transmit Silence"=YES)!
; Note that Xlite sends NAT keep-alive packets, so qualify=yes is not needed
;type=friend
;regexten=1234                   ; When they register, create extension 1234
;callerid="Jane Smith" <5678>
;host=dynamic                    ; This device needs to register
;directmedia=no                  ; Typically set to NO if behind NAT
;disallow=all
;allow=gsm                       ; GSM consumes far less bandwidth than ulaw
;allow=ulaw
;allow=alaw
;mailbox=1234@default,1233@default ; Subscribe to status of multiple mailboxes

;[snom]
;type=friend                     ; Friends place calls and receive calls
;context=from-sip                ; Context for incoming calls from this user
;secret=blah
;subscribecontext=localextensions ; Only allow SUBSCRIBE for local extensions
;language=de                     ; Use German prompts for this user
;host=dynamic                    ; This peer register with us
;dtmfmode=inband                 ; Choices are inband, rfc2833, or info
;defaultip=192.168.0.59          ; IP used until peer registers
;mailbox=1234@context,2345@context ; Mailbox(-es) for message waiting indicator
;subscribemwi=yes                ; Only send notifications if this phone
                                 ; subscribes for mailbox notification
;vmexten=voicemail               ; dialplan extension to reach mailbox
                                 ; sets the Message-Account in the MWI notify message
                                 ; defaults to global vmexten which defaults to "asterisk"
;disallow=all
;allow=ulaw                      ; dtmfmode=inband only works with ulaw or alaw!


;[polycom]
;type=friend                     ; Friends place calls and receive calls
;context=from-sip                ; Context for incoming calls from this user
;secret=blahpoly
;host=dynamic                    ; This peer register with us
;dtmfmode=rfc2833                ; Choices are inband, rfc2833, or info
;defaultuser=polly               ; Username to use in INVITE until peer registers
;defaultip=192.168.40.123
                                 ; Normally you do NOT need to set this parameter
;disallow=all
;allow=ulaw                      ; dtmfmode=inband only works with ulaw or alaw!
;progressinband=no               ; Polycom phones don't work properly with "never"


;[pingtel]
;type=friend
;secret=blah
;host=dynamic
;insecure=port                   ; Allow matching of peer by IP address without
                                 ; matching port number
;insecure=invite                 ; Do not require authentication of incoming INVITEs
;insecure=port,invite            ; (both)
;qualify=1000                    ; Consider it down if it's 1 second to reply
                                 ; Helps with NAT session
                                 ; qualify=yes uses default value
;qualifyfreq=60                  ; Qualification: How often to check for the
                                 ; host to be up in seconds
                                 ; Set to low value if you use low timeout for
                                 ; NAT of UDP sessions
;
; Call group and Pickup group should be in the range from 0 to 63
;
;callgroup=1,3-4                 ; We are in caller groups 1,3,4
;pickupgroup=1,3-5               ; We can do call pick-p for call group 1,3,4,5
;namedcallgroup=engineering,sales,netgroup,protgroup ; We are in named call groups engineering,sales,netgroup,protgroup
;namedpickupgroup=sales          ; We can do call pick-p for named call group sales
;defaultip=192.168.0.60          ; IP address to use if peer has not registered
;deny=0.0.0.0/0.0.0.0            ; ACL: Control access to this account based on IP address
;permit=192.168.0.60/255.255.255.0
;permit=192.168.0.60/24          ; we can also use CIDR notation for subnet masks
;permit=2001:db8::/32            ; IPv6 ACLs can be specified if desired. IPv6 ACLs
                                 ; apply only to IPv6 addresses, and IPv4 ACLs apply
                                 ; only to IPv4 addresses.
;acl=named_acl_example           ; Use named ACLs defined in acl.conf

;[cisco1]
;type=friend
;secret=blah
;qualify=200                     ; Qualify peer is no more than 200ms away
;host=dynamic                    ; This device registers with us
;directmedia=no                  ; Asterisk by default tries to redirect the
                                 ; RTP media stream (audio) to go directly from
                                 ; the caller to the callee.  Some devices do not
                                 ; support this (especially if one of them is
                                 ; behind a NAT).
;defaultip=192.168.0.4           ; IP address to use until registration
;defaultuser=goran               ; Username to use when calling this device before registration
                                 ; Normally you do NOT need to set this parameter
;setvar=CUSTID=5678              ; Channel variable to be set for all calls from or to this device
;setvar=ATTENDED_TRANSFER_COMPLETE_SOUND=beep   ; This channel variable will
                                                ; cause the given audio file to
                                                ; be played upon completion of
                                                ; an attended transfer to the
                                                ; target of the transfer.

;[pre14-asterisk]
;type=friend
;secret=digium
;host=dynamic
;rfc2833compensate=yes          ; Compensate for pre-1.4 DTMF transmission from another Asterisk machine.
                                ; You must have this turned on or DTMF reception will work improperly.
;t38pt_usertpsource=yes         ; Use the source IP address of RTP as the destination IP address for UDPTL packets
                                ; if the nat option is enabled. If a single RTP packet is received Asterisk will know the
                                ; external IP address of the remote device. If port forwarding is done at the client side
                                ; then UDPTL will flow to the remote device.



[fooprovider]
type=friend
secret=<?= $_ENV["SIP_SECRET"] ?>

username=<?= $_ENV["SIP_USERNAME"] ?>

host=<?= $_ENV["SIP_HOST"] ?>

dtmfmode=rfc2833
canreinvite=no
disallow=all
allow=ulaw
allow=alaw
allow=gsm
insecure=port,invite
fromdomain=<?= $_ENV["SIP_FROMDOMAIN"] ?>

;context=default
