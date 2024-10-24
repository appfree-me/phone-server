; PJSIP Configuration Samples and Quick Reference
;
; This file has several very basic configuration examples, to serve as a quick
; reference to jog your memory when you need to write up a new configuration.
; It is not intended to teach PJSIP configuration or serve as an exhaustive
; reference of options and potential scenarios.
;
; This file has two main sections.
; First, manually written examples to serve as a handy reference.
; Second, a list of all possible PJSIP config options by section. This is
; pulled from the XML config help. It only shows the synopsis for every item.
; If you want to see more detail please check the documentation sources
; mentioned at the top of this file.

; ============================================================================
; NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE NOTICE
;
; This file does not maintain the complete option documentation.
; ============================================================================

; Documentation
;
; The official documentation is at http://wiki.asterisk.org
; You can read the XML configuration help via Asterisk command line with
; "config show help res_pjsip", then you can drill down through the various
; sections and their options.
;

;========!!!!!!!!!!!!!!!!!!!  SECURITY NOTICE  !!!!!!!!!!!!!!!!!!!!===========
;
; At a minimum please read the file "README-SERIOUSLY.bestpractices.txt",
; located in the Asterisk source directory before starting Asterisk.
; Otherwise you risk allowing the security of the Asterisk system to be
; compromised. Beyond that please visit and read the security information on
; the wiki at: https://wiki.asterisk.org/wiki/x/EwFB
;
; A few basics to pay attention to:
;
; Anonymous Calls
;
; By default anonymous inbound calls via PJSIP are not allowed. If you want to
; route anonymous calls you'll need to define an endpoint named "anonymous".
; res_pjsip_endpoint_identifier_anonymous.so handles that functionality so it
; must be loaded. It is not recommended to accept anonymous calls.
;
; Access Control Lists
;
; See the example ACL configuration in this file. Read the configuration help
; for the section and all of its options. Look over the samples in acl.conf
; and documentation at https://wiki.asterisk.org/wiki/x/uA80AQ
; If possible, restrict access to only networks and addresses you trust.
;
; Dialplan Contexts
;
; When defining configuration (such as an endpoint) that links into
; dialplan configuration, be aware of what that dialplan does. It's easy to
; accidentally provide access to internal or outbound dialing extensions which
; could cost you severely. The "context=" line in endpoint configuration
; determines which dialplan context inbound calls will enter into.
;
;=============================================================================

; Overview of Configuration Section Types Used in the Examples
;
; * Transport "transport"
;   * Configures res_pjsip transport layer interaction.
; * Endpoint "endpoint"
;   * Configures core SIP functionality related to SIP endpoints.
; * Authentication "auth"
;   * Stores inbound or outbound authentication credentials for use by trunks,
;     endpoints, registrations.
; * Address of Record "aor"
;   * Stores contact information for use by endpoints.
; * Endpoint Identification "identify"
;   * Maps a host directly to an endpoint
; * Access Control List "acl"
;   * Defines a permission list or references one stored in acl.conf
; * Registration "registration"
;   * Contains information about an outbound SIP registration
; * Resource Lists
;   * Contains information for configuring resource lists.
; * Phone Provisioning "phoneprov"
;   * Contains information needed by res_phoneprov for autoprovisioning

; The following sections show example configurations for various scenarios.
; Most require a couple or more configuration types configured in concert.

;=============================================================================

; Naming of Configuration Sections
;
; Configuration section names are denoted with enclosing brackets,
; e.g. [6001]
; In most cases, you can name a section whatever makes sense to you. For example
; you might name a transport [transport-udp-nat] to help you remember how that
; section is being used. However, in some cases, ("endpoint" and "aor" types)
; the section name has a relationship to its function.
;
; Depending on the modules loaded, Asterisk can match SIP requests to an
; endpoint or aor in a few ways:
;
; 1) Match a section name for endpoint type sections to the username in the
;    "From" header of inbound SIP requests.
; 2) Match a section name for aor type sections to the username in the "To"
;    header of inbound SIP REGISTER requests.
; 3) With an identify type section configured, match an inbound SIP request of
;    any type to an endpoint or aor based on the IP source address of the
;    request.
;
; Note that sections can have the same name as long as their "type" options are
; set to different values. In most cases it makes sense to have associated
; configuration sections use the same name, as you'll see in the examples within
; this file.

;===============EXAMPLE TRANSPORTS============================================
;
; A few examples for potential transport options.
;
; For the NAT transport example, be aware that the options starting with
; the prefix "external_" will only apply to communication with addresses
; outside the range set with "local_net=".
;
; You can have more than one of any type of transport, as long as it doesn't
; use the same resources (bind address, port, etc) as the others.

; Basic UDP transport
;
;[transport-udp]
;type=transport
;protocol=udp    ;udp,tcp,tls,ws,wss,flow
;bind=0.0.0.0

; UDP transport behind NAT
;
;[transport-udp-nat]
;type=transport
;protocol=udp
;bind=0.0.0.0
;local_net=192.0.2.0/24
;external_media_address=203.0.113.1
;external_signaling_address=203.0.113.1

; Basic IPv6 UDP transport
;
;[transport-udp-ipv6]
;type=transport
;protocol=udp
;bind=::

; Example IPv4 TLS transport
;
;[transport-tls]
;type=transport
;protocol=tls
;bind=0.0.0.0
;cert_file=/path/mycert.crt
;priv_key_file=/path/mykey.key
;cipher=ADH-AES256-SHA,ADH-AES128-SHA
;method=tlsv1

; Example flow transport
;
; A flow transport is used to reference a flow of signaling with a specific
; target. All endpoints or other objects that reference the transport will use
; the same underlying transport and can share runtime discovered transport
; configuration (such as service routes). The protocol in use will be determined
; based on the URI used to establish the connection. Currently only TCP and TLS
; are supported.
;
;[transport-flow]
;type=transport
;protocol=flow

;===============OUTBOUND REGISTRATION WITH OUTBOUND AUTHENTICATION============
;
; This is a simple registration that works with some SIP trunking providers.
; You'll need to set up the auth example "mytrunk_auth" below to enable outbound
; authentication. Note that we "outbound_auth=" use for outbound authentication
; instead of "auth=", which is for inbound authentication.
;
; If you are registering to a server from behind NAT, be sure you assign a transport
; that is appropriately configured with NAT related settings. See the NAT transport example.
;
; "contact_user=" sets the SIP contact header's user portion of the SIP URI
; this will affect the extension reached in dialplan when the far end calls you at this
; registration. The default is 's'.
;
; If you would like to enable line support and have incoming calls related to this
; registration go to an endpoint automatically the "line" and "endpoint" options must
; be set. The "endpoint" option specifies what endpoint the incoming call should be
; associated with.

;[mytrunk]
;type=registration
;transport=transport-udp
;outbound_auth=mytrunk_auth
;server_uri=sip:sip.example.com
;client_uri=sip:1234567890@sip.example.com
;contact_user=1234567890
;retry_interval=60
;forbidden_retry_interval=600
;expiration=3600
;line=yes
;endpoint=mytrunk

;[mytrunk_auth]
;type=auth
;auth_type=userpass
;password=1234567890
;username=1234567890
;realm=sip.example.com

;===============ENDPOINT CONFIGURED AS A TRUNK, OUTBOUND AUTHENTICATION=======
;
; This is one way to configure an endpoint as a trunk. It is set up with
; "outbound_auth=" to enable authentication when dialing out through this
; endpoint. There is no inbound authentication set up since a provider will
; not normally authenticate when calling you.
;
; The identify configuration enables IP address matching against this endpoint.
; For calls from a trunking provider, the From user may be different every time,
; so we want to match against IP address instead of From user.
;
; If you want the provider of your trunk to know where to send your calls
; you'll need to use an outbound registration as in the example above this
; section.
;
; NAT
;
; At a basic level configure the endpoint with a transport that is set up
; with the appropriate NAT settings. There may be some additional settings you
; need here based on your NAT/Firewall scenario. Look to the CLI config help
; "config show help res_pjsip endpoint" or on the wiki for other NAT related
; options and configuration. We've included a few below.
;
; AOR
;
; Endpoints use one or more AOR sections to store their contact details.
; You can define multiple contact addresses in SIP URI format in multiple
; "contact=" entries.
;

;[mytrunk]
;type=endpoint
;transport=transport-udp
;context=from-external
;disallow=all
;allow=ulaw
;outbound_auth=mytrunk_auth
;aors=mytrunk
;                   ;A few NAT relevant options that may come in handy.
;force_rport=yes    ;It's a good idea to read the configuration help for each
;direct_media=no    ;of these options.
;ice_support=yes

;[mytrunk]
;type=aor
;contact=sip:198.51.100.1:5060
;contact=sip:198.51.100.2:5060

;[mytrunk]
;type=identify
;endpoint=mytrunk
;match=198.51.100.1
;match=198.51.100.2
;match=192.168.10.0:5061/24


;=============ENDPOINT CONFIGURED AS A TRUNK, INBOUND AUTH AND REGISTRATION===
;
; Here we are allowing a remote device to register to Asterisk and requiring
; that they authenticate for registration and calls.
; You'll note that this configuration is essentially the same as configuring
; an endpoint for use with a SIP phone.


;[7000]
;type=endpoint
;context=from-external
;disallow=all
;allow=ulaw
;transport=transport-udp
;auth=7000
;aors=7000

;[7000]
;type=auth
;auth_type=userpass
;password=7000
;username=7000

;[7000]
;type=aor
;max_contacts=1


;===============ENDPOINT CONFIGURED FOR USE WITH A SIP PHONE==================
;
; This example includes the endpoint, auth and aor configurations. It
; requires inbound authentication and allows registration, as well as references
; a transport that you'll need to uncomment from the previous examples.
;
; Uncomment one of the transport lines to choose which transport you want. If
; not specified then the default transport chosen is the first compatible transport
; in the configuration file for the contact URL.
;
; Modify the "max_contacts=" line to change how many unique registrations to allow.
;
; Use the "contact=" line instead of max_contacts= if you want to statically
; define the location of the device.
;
; If using the TLS enabled transport, you may want the "media_encryption=sdes"
; option to additionally enable SRTP, though they are not mutually inclusive.
;
; If this endpoint were remote, and it was using a transport configured for NAT
; then you likely want to use "direct_media=no" to prevent audio issues.


;[6001]
;type=endpoint
;transport=transport-udp
;context=from-internal
;disallow=all
;allow=ulaw
;allow=gsm
;auth=6001
;aors=6001
;
; A few more transports to pick from, and some related options below them.
;
;transport=transport-tls
;media_encryption=sdes
;transport=transport-udp-ipv6
;transport=transport-udp-nat
;direct_media=no
;
; MWI related options

;aggregate_mwi=yes
;mailboxes=6001@default,7001@default
;mwi_from_user=6001
;
; Extension and Device state options
;
;device_state_busy_at=1
;allow_subscribe=yes
;sub_min_expiry=30
;
; STIR/SHAKEN support.
;
;stir_shaken=no

;[6001]
;type=auth
;auth_type=userpass
;password=6001
;username=6001

;[6001]
;type=aor
;max_contacts=1
;contact=sip:6001@192.0.2.1:5060

;===============ENDPOINT BEHIND NAT OR FIREWALL===============================
;
; This example assumes your transport is configured with a public IP and the
; endpoint itself is behind NAT and maybe a firewall, rather than having
; Asterisk behind NAT. For the sake of simplicity, we'll assume a typical
; VOIP phone. The most important settings to configure are:
;
;  * direct_media, to ensure Asterisk stays in the media path
;  * rtp_symmetric and force_rport options to help the far-end NAT/firewall
;
; Depending on the settings of your remote SIP device or NAT/firewall device
; you may have to experiment with a combination of these settings.
;
; If both Asterisk and the remote phones are a behind NAT/firewall then you'll
; have to make sure to use a transport with appropriate settings (as in the
; transport-udp-nat example).
;
;[6002]
;type=endpoint
;transport=transport-udp
;context=from-internal
;disallow=all
;allow=ulaw
;auth=6002
;aors=6002
;direct_media=no
;rtp_symmetric=yes
;force_rport=yes
;rewrite_contact=yes  ; necessary if endpoint does not know/register public ip:port
;ice_support=yes   ;This is specific to clients that support NAT traversal
                   ;for media via ICE,STUN,TURN. See the wiki at:
                   ;https://wiki.asterisk.org/wiki/x/D4FHAQ
                   ;for a deeper explanation of this topic.

;[6002]
;type=auth
;auth_type=userpass
;password=6002
;username=6002

;[6002]
;type=aor
;max_contacts=2


;============EXAMPLE ACL CONFIGURATION==========================================
;
; The ACL or Access Control List section defines a set of permissions to permit
; or deny access to various address or addresses. Alternatively it references an
; ACL configuration already set in acl.conf.
;
; The ACL configuration is independent of individual endpoint configuration and
; operates on all inbound SIP communication using res_pjsip.

; Reference an ACL defined in acl.conf.
;
;[acl]
;type=acl
;acl=example_named_acl1

; Reference a contactacl specifically.
;
;[acl]
;type=acl
;contact_acl=example_contact_acl1

; Define your own ACL here in pjsip.conf and
; permit or deny by IP address or range.
;
;[acl]
;type=acl
;deny=0.0.0.0/0.0.0.0
;permit=209.16.236.0/24
;deny=209.16.236.1

; Restrict based on Contact Headers rather than IP.
; Define options multiple times for various addresses or use a comma-delimited string.
;
;[acl]
;type=acl
;contact_deny=0.0.0.0/0.0.0.0
;contact_permit=209.16.236.0/24
;contact_permit=209.16.236.1
;contact_permit=209.16.236.2,209.16.236.3

; Restrict based on Contact Headers rather than IP and use
; advanced syntax. Note the bang symbol used for "NOT", so we can deny
; 209.16.236.12/32 within the permit= statement.
;
;[acl]
;type=acl
;contact_deny=0.0.0.0/0.0.0.0
;contact_permit=209.16.236.0
;permit=209.16.236.0/24, !209.16.236.12/32


;============EXAMPLE RLS CONFIGURATION==========================================
;
;Asterisk provides support for RFC 4662 Resource List Subscriptions. This allows
;for an endpoint to, through a single subscription, subscribe to the states of
;multiple resources. Resource lists are configured in pjsip.conf using the
;resource_list configuration object. Below is an example of a resource list that
;allows an endpoint to subscribe to the presence of alice, bob, and carol.

;[my_list]
;type=resource_list
;list_item=alice
;list_item=bob
;list_item=carol
;event=presence

;The "event" option in the resource list corresponds to the SIP event-package
;that the subscribed resources belong to. A resource list can only provide states
;for resources that belong to the same event-package. This means that you cannot
;create a list that is a combination of presence and message-summary resources,
;for instance. Any event-package that Asterisk supports can be used in a resource
;list (presence, dialog, and message-summary). Whenever support for a new event-
;package is added to Asterisk, support for that event-package in resource lists
;will automatically be supported.

;The "list_item" options indicate the names of resources to subscribe to. The
;way these are interpreted is event-package specific. For instance, with presence
;list_items, hints in the dialplan are looked up. With message-summary list_items,
;mailboxes are looked up using your installed voicemail provider (app_voicemail
;by default). Note that in the above example, the list_item options were given
;one per line. However, it is also permissible to provide multiple list_item
;options on a single line (e.g. list_item = alice,bob,carol).

;In addition to the options presented in the above configuration, there are two
;more configuration options that can be set.
; * full_state: dictates whether Asterisk should always send the states of
;   all resources in the list at once. Defaults to "no". You should only set
;   this to "yes" if you are interoperating with an endpoint that does not
;   behave correctly when partial state notifications are sent to it.
; * notification_batch_interval: By default, Asterisk will send a NOTIFY request
;   immediately when a resource changes state. This option causes Asterisk to
;   start batching resource state changes for the specified number of milliseconds
;   after a resource changes states. This way, if multiple resources change state
;   within a brief interval, Asterisk can send a single NOTIFY request with all
;   of the state changes reflected in it.

;There is a limitation to the size of resource lists in Asterisk. If a constructed
;notification from Asterisk will exceed 64000 bytes, then the message is deemed
;too large to send. If you find that you are seeing error messages about SIP
;NOTIFY requests being too large to send, consider breaking your lists into
;sub-lists.

;============EXAMPLE PHONEPROV CONFIGURATION================================

; Before configuring provisioning here, see the documentation for res_phoneprov
; and configure phoneprov.conf appropriately.

; For each user to be autoprovisioned, a [phoneprov] configuration section
; must be created.  At a minimum, the 'type', 'PROFILE' and 'MAC' variables must
; be set.  All other variables are optional.
; Example:

;[1000]
;type=phoneprov               ; must be specified as 'phoneprov'
;endpoint=1000                ; Required only if automatic setting of
                              ; USERNAME, SECRET, DISPLAY_NAME and CALLERID
                              ; are needed.
;PROFILE=digium               ; required
;MAC=deadbeef4dad             ; required
;SERVER=myserver.example.com  ; A standard variable
;TIMEZONE=America/Denver      ; A standard variable
;MYVAR=somevalue              ; A user confdigured variable

; If the phoneprov sections have common variables, it is best to create a
; phoneprov template.  The example below will produce the same configuration
; as the one specified above except that MYVAR will be overridden for
; the specific user.
; Example:

;[phoneprov_defaults](!)
;type=phoneprov               ; must be specified as 'phoneprov'
;PROFILE=digium               ; required
;SERVER=myserver.example.com  ; A standard variable
;TIMEZONE=America/Denver      ; A standard variable
;MYVAR=somevalue              ; A user configured variable

;[1000](phoneprov_defaults)
;endpoint=1000                ; Required only if automatic setting of
                              ; USERNAME, SECRET, DISPLAY_NAME and CALLERID
                              ; are needed.
;MAC=deadbeef4dad             ; required
;MYVAR=someOTHERvalue         ; A user confdigured variable

; To have USERNAME and SECRET automatically set, the endpoint
; specified here must in turn have an outbound_auth section defined.

; Fuller example:

;[1000]
;type=endpoint
;outbound_auth=1000-auth
;callerid=My Name <8005551212>
;transport=transport-udp-nat

;[1000-auth]
;type=auth
;auth_type=userpass
;username=myname
;password=mysecret

;[phoneprov_defaults](!)
;type=phoneprov               ; must be specified as 'phoneprov'
;PROFILE=someprofile          ; required
;SERVER=myserver.example.com  ; A standard variable
;TIMEZONE=America/Denver      ; A standard variable
;MYVAR=somevalue              ; A user configured variable

;[1000](phoneprov_defaults)
;endpoint=1000                ; Required only if automatic setting of
                              ; USERNAME, SECRET, DISPLAY_NAME and CALLERID
                              ; are needed.
;MAC=deadbeef4dad             ; required
;MYVAR=someUSERvalue          ; A user confdigured variable
;LABEL=1000                   ; A standard variable

; The previous sections would produce a template substitution map as follows:

;MAC=deadbeef4dad               ;added by pp1000
;USERNAME=myname                ;automatically added by 1000-auth username
;SECRET=mysecret                ;automatically added by 1000-auth password
;PROFILE=someprofile            ;added by defaults
;SERVER=myserver.example.com    ;added by defaults
;SERVER_PORT=5060               ;added by defaults
;MYVAR=someUSERvalue            ;added by defaults but overdidden by user
;CALLERID=8005551212            ;automatically added by 1000 callerid
;DISPLAY_NAME=My Name           ;automatically added by 1000 callerid
;TIMEZONE=America/Denver        ;added by defaults
;TZOFFSET=252100                ;automatically calculated by res_phoneprov
;DST_ENABLE=1                   ;automatically calculated by res_phoneprov
;DST_START_MONTH=3              ;automatically calculated by res_phoneprov
;DST_START_MDAY=9               ;automatically calculated by res_phoneprov
;DST_START_HOUR=3               ;automatically calculated by res_phoneprov
;DST_END_MONTH=11               ;automatically calculated by res_phoneprov
;DST_END_MDAY=2                 ;automatically calculated by res_phoneprov
;DST_END_HOUR=1                 ;automatically calculated by res_phoneprov
;ENDPOINT_ID=1000               ;automatically added by this module
;AUTH_ID=1000-auth              ;automatically added by this module
;TRANSPORT_ID=transport-udp-nat ;automatically added by this module
;LABEL=1000                     ;added by user

; MODULE PROVIDING BELOW SECTION(S): res_pjsip
;==========================ENDPOINT SECTION OPTIONS=========================
;[endpoint]
;  SYNOPSIS: Endpoint
;100rel=yes     ; Allow support for RFC3262 provisional ACK tags (default:
                ; "yes")
;aggregate_mwi=yes      ;  (default: "yes")
;allow= ; Media Codec s to allow (default: "")
;allow_overlap=yes ; Enable RFC3578 overlap dialing support. (default: "yes")
;aors=  ; AoR s to be used with the endpoint (default: "")
;auth=  ; Authentication Object s associated with the endpoint (default: "")
;callerid=      ; CallerID information for the endpoint (default: "")
;callerid_privacy=allowed_not_screened      ; Default privacy level (default: "allowed_not_screened")
;callerid_tag=  ; Internal id_tag for the endpoint (default: "")
;context=default        ; Dialplan context for inbound sessions (default:
                        ; "default")
;direct_media_glare_mitigation=none     ; Mitigation of direct media re INVITE
                                        ; glare (default: "none")
;direct_media_method=invite     ; Direct Media method type (default: "invite")
;trust_connected_line=yes       ; Accept Connected Line updates from this endpoint
                                ; (default: "yes")
;send_connected_line=yes        ; Send Connected Line updates to this endpoint
                                ; (default: "yes")
;connected_line_method=invite   ; Connected line method type.
                                ; When set to "invite", check the remote's
                                ; Allow header and if UPDATE is allowed, send
                                ; UPDATE instead of INVITE to avoid SDP
                                ; renegotiation.  If UPDATE is not Allowed,
                                ; send INVITE.
                                ; If set to "update", send UPDATE regardless
                                ; of what the remote Allows.
                                ; (default: "invite")
;direct_media=yes       ; Determines whether media may flow directly between
                        ; endpoints (default: "yes")
;disable_direct_media_on_nat=no ; Disable direct media session refreshes when
                                ; NAT obstructs the media session (default:
                                ; "no")
;disallow=      ; Media Codec s to disallow (default: "")
;dtmf_mode=rfc4733      ; DTMF mode (default: "rfc4733")
;media_address=         ; IP address used in SDP for media handling (default: "")
;bind_rtp_to_media_address=     ; Bind the RTP session to the media_address.
                                ; This causes all RTP packets to be sent from
                                ; the specified address. (default: "no")
;force_rport=yes        ; Force use of return port (default: "yes")
;ice_support=no ; Enable the ICE mechanism to help traverse NAT (default: "no")
;identify_by=username   ; A comma-separated list of ways the Endpoint or AoR can be
                        ; identified.
                        ; "username": Identify by the From or To username and domain
                        ; "auth_username": Identify by the Authorization username and realm
                        ; "ip": Identify by the source IP address
                        ; "header": Identify by a configured SIP header value.
                        ; In the username and auth_username cases, if an exact match
                        ; on both username and domain/realm fails, the match is
                        ; retried with just the username.
                        ; (default: "username,ip")
;redirect_method=user   ; How redirects received from an endpoint are handled
                        ; (default: "user")
;mailboxes=     ; NOTIFY the endpoint when state changes for any of the specified mailboxes.
                ; Asterisk will send unsolicited MWI NOTIFY messages to the endpoint when state
                ; changes happen for any of the specified mailboxes. (default: "")
;voicemail_extension= ; The voicemail extension to send in the NOTIFY Message-Account header
                      ; (default: global/default_voicemail_extension)
;mwi_subscribe_replaces_unsolicited=no
                      ; An MWI subscribe will replace unsoliticed NOTIFYs
                      ; (default: "no")
;moh_suggest=default    ; Default Music On Hold class (default: "default")
;moh_passthrough=yes    ; Pass Music On Hold through using SIP re-invites with sendonly
                        ; when placing on hold and sendrecv when taking off hold
;outbound_auth= ; Authentication object used for outbound requests (default:
                ; "")
;outbound_proxy=        ; Proxy through which to send requests, a full SIP URI
                        ; must be provided (default: "")
;rewrite_contact=no     ; Allow Contact header to be rewritten with the source
                        ; IP address port (default: "no")
;rtp_symmetric=no       ; Enforce that RTP must be symmetric (default: "no")
;send_diversion=yes     ; Send the Diversion header conveying the diversion
                        ; information to the called user agent (default: "yes")
;send_pai=no    ; Send the P Asserted Identity header (default: "no")
;send_rpid=no   ; Send the Remote Party ID header (default: "no")
;rpid_immediate=no      ; Send connected line updates on unanswered incoming calls immediately. (default: "no")
;timers_min_se=90       ; Minimum session timers expiration period (default:
                        ; "90")
;timers=yes     ; Session timers for SIP packets (default: "yes")
;timers_sess_expires=1800       ; Maximum session timer expiration period
                                ; (default: "1800")
;transport=     ; Explicit transport configuration to use (default: "")
                ; This will force the endpoint to use the specified transport
                ; configuration to send SIP messages.  You need to already know
                ; what kind of transport (UDP/TCP/IPv4/etc) the endpoint device
                ; will use.

;trust_id_inbound=no    ; Accept identification information received from this
                        ; endpoint (default: "no")
;trust_id_outbound=no   ; Send private identification details to the endpoint
                        ; (default: "no")
;type=  ; Must be of type endpoint (default: "")
;use_ptime=no   ; Use Endpoint s requested packetisation interval (default:
                ; "no")
;use_avpf=no    ; Determines whether res_pjsip will use and enforce usage of
                ; AVPF for this endpoint (default: "no")
;media_encryption=no    ; Determines whether res_pjsip will use and enforce
                        ; usage of media encryption for this endpoint (default:
                        ; "no")
;media_encryption_optimistic=no ; Use encryption if possible but don't fail the call
                                ; if not possible.
;g726_non_standard=no   ; When set to "yes" and an endpoint negotiates g.726
                        ; audio then g.726 for AAL2 packing order is used contrary
                        ; to what is recommended in RFC3551. Note, 'g726aal2' also
                        ; needs to be specified in the codec allow list
                        ; (default: "no")
;inband_progress=no     ; Determines whether chan_pjsip will indicate ringing
                        ; using inband progress (default: "no")
;call_group=    ; The numeric pickup groups for a channel (default: "")
;pickup_group=  ; The numeric pickup groups that a channel can pickup (default:
                ; "")
;named_call_group=      ; The named pickup groups for a channel (default: "")
;named_pickup_group=    ; The named pickup groups that a channel can pickup
                        ; (default: "")
;device_state_busy_at=0 ; The number of in use channels which will cause busy
                        ; to be returned as device state (default: "0")
;t38_udptl=no   ; Whether T 38 UDPTL support is enabled or not (default: "no")
;t38_udptl_ec=none      ; T 38 UDPTL error correction method (default: "none")
;t38_udptl_maxdatagram=0        ; T 38 UDPTL maximum datagram size (default:
                                ; "0")
;fax_detect=no  ; Whether CNG tone detection is enabled (default: "no")
;fax_detect_timeout=30  ; How many seconds into a call before fax_detect is
                        ; disabled for the call.
                        ; Zero disables the timeout.
                        ; (default: "0")
;t38_udptl_nat=no       ; Whether NAT support is enabled on UDPTL sessions
                        ; (default: "no")
;t38_bind_rtp_to_media_address=     ; Bind the UDPTL session to the media_address.
                                    ; This causes all UDPTL packets to be sent from
                                    ; the specified address. (default: "no")
;tone_zone=     ; Set which country s indications to use for channels created
                ; for this endpoint (default: "")
;language=      ; Set the default language to use for channels created for this
                ; endpoint (default: "")
;one_touch_recording=no ; Determines whether one touch recording is allowed for
                        ; this endpoint (default: "no")
;record_on_feature=automixmon   ; The feature to enact when one touch recording
                                ; is turned on (default: "automixmon")
;record_off_feature=automixmon  ; The feature to enact when one touch recording
                                ; is turned off (default: "automixmon")
;rtp_engine=asterisk    ; Name of the RTP engine to use for channels created
                        ; for this endpoint (default: "asterisk")
;allow_transfer=yes     ; Determines whether SIP REFER transfers are allowed
                        ; for this endpoint (default: "yes")
;sdp_owner=-    ; String placed as the username portion of an SDP origin o line
                ; (default: "-")
;sdp_session=Asterisk   ; String used for the SDP session s line (default:
                        ; "Asterisk")
;tos_audio=0    ; DSCP TOS bits for audio streams (default: "0")
;tos_video=0    ; DSCP TOS bits for video streams (default: "0")
;cos_audio=0    ; Priority for audio streams (default: "0")
;cos_video=0    ; Priority for video streams (default: "0")
;allow_subscribe=yes    ; Determines if endpoint is allowed to initiate
                        ; subscriptions with Asterisk (default: "yes")
;sub_min_expiry=0       ; The minimum allowed expiry time for subscriptions
                        ; initiated by the endpoint (default: "0")
;from_user=     ; Username to use in From header for requests to this endpoint
                ; (default: "")
;mwi_from_user= ; Username to use in From header for unsolicited MWI NOTIFYs to
                ; this endpoint (default: "")
;from_domain=   ; Domain to user in From header for requests to this endpoint
                ; (default: "")
;dtls_verify=no ; Verify that the provided peer certificate is valid (default:
                ; "no")
;dtls_rekey=0   ; Interval at which to renegotiate the TLS session and rekey
                ; the SRTP session (default: "0")
;dtls_auto_generate_cert= ; Enable ephemeral DTLS certificate generation (default:
                          ; "no")
;dtls_cert_file=          ; Path to certificate file to present to peer (default:
                          ; "")
;dtls_private_key=        ; Path to private key for certificate file (default:
                          ; "")
;dtls_cipher=   ; Cipher to use for DTLS negotiation (default: "")
;dtls_ca_file=  ; Path to certificate authority certificate (default: "")
;dtls_ca_path=  ; Path to a directory containing certificate authority
                ; certificates (default: "")
;dtls_setup=    ; Whether we are willing to accept connections connect to the
                ; other party or both (default: "")
;dtls_fingerprint= ; Hash to use for the fingerprint placed into SDP
                   ; (default: "SHA-256")
;srtp_tag_32=no ; Determines whether 32 byte tags should be used instead of 80
                ; byte tags (default: "no")
;set_var=       ; Variable set on a channel involving the endpoint. For multiple
                ; channel variables specify multiple 'set_var'(s)
;rtp_keepalive= ; Interval, in seconds, between comfort noise RTP packets if
                ; RTP is not flowing. This setting is useful for ensuring that
                ; holes in NATs and firewalls are kept open throughout a call.
;rtp_timeout=      ; Hang up channel if RTP is not received for the specified
                   ; number of seconds when the channel is off hold (default:
                   ; "0" or not enabled)
;rtp_timeout_hold= ; Hang up channel if RTP is not received for the specified
                   ; number of seconds when the channel is on hold (default:
                   ; "0" or not enabled)
;contact_user= ; On outgoing requests, force the user portion of the Contact
               ; header to this value (default: "")
;incoming_call_offer_pref= ; Based on this setting, a joint list of
                           ; preferred codecs between those received in an
                           ; incoming SDP offer (remote), and those specified
                           ; in the endpoint's "allow" parameter (local)
                           ; is created and is passed to the Asterisk core.
                           ;
                           ; local - Include all codecs in the local list that
                           ; are also in the remote list preserving the local
                           ; order. (default).
                           ; local_first - Include only the first codec in the
                           ; local list that is also in the remote list.
                           ; remote - Include all codecs in the remote list that
                           ; are also in the local list preserving remote list
                           ; order.
                           ; remote_first - Include only the first codec in
                           ; the remote list that is also in the local list.
;outgoing_call_offer_pref= ; Based on this setting, a joint list of
                           ; preferred codecs between those received from the
                           ; Asterisk core (remote), and those specified in
                           ; the endpoint's "allow" parameter (local) is
                           ; created and is used to create the outgoing SDP
                           ; offer.
                           ;
                           ; local - Include all codecs in the local list that
                           ; are also in the remote list preserving the local
                           ; order.
                           ; local_merge - Include all codecs in the local list
                           ; preserving the local order.
                           ; local_first - Include only the first codec in the
                           ; local list.
                           ; remote - Include all codecs in the remote list that
                           ; are also in the local list preserving remote list
                           ; order.
                           ; remote_merge - Include all codecs in the local list
                           ; preserving the remote list order. (default)
                           ; remote_first - Include only the first codec in the
                           ; remote list that is also in the local list.
;preferred_codec_only=no   ; Respond to a SIP invite with the single most
                           ; preferred codec rather than advertising all joint
                           ; codec capabilities. This limits the other side's
                           ; codec choice to exactly what we prefer.
                           ; default is no.
                           ; NOTE: This option is deprecated in favor
                           ; of incoming_call_offer_pref.  Setting both
                           ; options is unsupported.
;asymmetric_rtp_codec= ; Allow the sending and receiving codec to differ and
                       ; not be automatically matched (default: "no")
;refer_blind_progress= ; Whether to notifies all the progress details on blind
                       ; transfer (default: "yes"). The value "no" is useful
                       ; for some SIP phones (Mitel/Aastra, Snom) which expect
                       ; a sip/frag "200 OK" after REFER has been accepted.
;notify_early_inuse_ringing = ; Whether to notifies dialog-info 'early'
                              ; on INUSE && RINGING state (default: "no").
                              ; The value "yes" is useful for some SIP phones
                              ; (Cisco SPA) to be able to indicate and pick up
                              ; ringing devices.
;max_audio_streams= ; The maximum number of allowed negotiated audio streams
                    ; (default: 1)
;max_video_streams= ; The maximum number of allowed negotiated video streams
                    ; (default: 1)
;webrtc= ; When set to "yes" this also enables the following values that are needed
         ; for webrtc: rtcp_mux, use_avpf, ice_support, and use_received_transport.
         ; The following configuration settings also get defaulted as follows:
         ;     media_encryption=dtls
         ;     dtls_verify=fingerprint
         ;     dtls_setup=actpass
         ; A dtls_cert_file and a dtls_ca_file still need to be specified.
         ; Default for this option is "no"
;incoming_mwi_mailbox = ; Mailbox name to use when incoming MWI NOTIFYs are
                        ; received.
                        ; If an MWI NOTIFY is received FROM this endpoint,
                        ; this mailbox will be used when notifying other modules
                        ; of MWI status changes.  If not set, incoming MWI
                        ; NOTIFYs are ignored.
;follow_early_media_fork = ; On outgoing calls, if the UAS responds with
                           ; different SDP attributes on subsequent 18X or 2XX
                           ; responses (such as a port update) AND the To tag
                           ; on the subsequent response is different than that
                           ; on the previous one, follow it.  This usually
                           ; happens when the INVITE is forked to multiple UASs
                           ; and more than 1 sends an SDP answer.
                           ; This option must also be enabled in the system
                           ; section.
                           ; (default: yes)
;accept_multiple_sdp_answers =
                           ; On outgoing calls, if the UAS responds with
                           ; different SDP attributes on non-100rel 18X or 2XX
                           ; responses (such as a port update) AND the To tag on
                           ; the subsequent response is the same as that on the
                           ; previous one, process it. This can happen when the
                           ; UAS needs to change ports for some reason such as
                           ; using a separate port for custom ringback.
                           ; This option must also be enabled in the system
                           ; section.
                           ; (default: no)
;suppress_q850_reason_headers =
                           ; Suppress Q.850 Reason headers for this endpoint.
                           ; Some devices can't accept multiple Reason headers
                           ; and get confused when both 'SIP' and 'Q.850' Reason
                           ; headers are received.  This option allows the
                           ; 'Q.850' Reason header to be suppressed.
                           ; (default: no)
;ignore_183_without_sdp =
                           ; Do not forward 183 when it doesn't contain SDP.
                           ; Certain SS7 internetworking scenarios can result in
                           ; a 183 to be generated for reasons other than early
                           ; media.  Forwarding this 183 can cause loss of
                           ; ringback tone.  This flag emulates the behavior of
                           ; chan_sip and prevents these 183 responses from
                           ; being forwarded.
                           ; (default: no)
;stir_shaken =
                           ; If this is enabled, STIR/SHAKEN operations will be
                           ; performed on this endpoint. This includes inbound
                           ; and outbound INVITEs. On an inbound INVITE, Asterisk
                           ; will check for an Identity header and attempt to
                           ; verify the call. On an outbound INVITE, Asterisk will
                           ; add an Identity header that others can use to verify
                           ; calls from this endpoint. Additional configuration is
                           ; done in stir_shaken.conf.
                           ; The STIR_SHAKEN dialplan function must be used to get
                           ; the verification results on inbound INVITEs. Nothing
                           ; happens to the call if verification fails; it's up to
                           ; you to determine what to do with the results.
                           ; (default: no)
;allow_unauthenticated_options =
                           ; By default, chan_pjsip will challenge an incoming
                           ; OPTIONS request for authentication credentials just
                           ; as it would an INVITE request. This is consistent
                           ; with RFC 3261.
                           ; There are many UAs that use an OPTIONS request as a
                           ; "ping" and they expect a 200 response indicating that
                           ; the remote party is up and running without a need to
                           ; authenticate.
                           ; Setting allow_unauthenticated_options to 'yes' will
                           ; instruct chan_pjsip to skip the authentication step
                           ; when it receives an OPTIONS request for this
                           ; endpoint.
                           ; There are security implications to enabling this
                           ; setting as it can allow information disclosure to
                           ; occur - specifically, if enabled, an external party
                           ; could enumerate and find the endpoint name by
                           ; sending OPTIONS requests and examining the
                           ; responses.
                           ; (default: no)

;==========================AUTH SECTION OPTIONS=========================
;[auth]
;  SYNOPSIS: Authentication type
;
;  Note: Using the same auth section for inbound and outbound
;  authentication is not recommended.  There is a difference in
;  meaning for an empty realm setting between inbound and outbound
;  authentication uses.  Look to the CLI config help
;  "config show help res_pjsip auth realm" or on the wiki for the
;  difference.
;
;auth_type=userpass  ; Authentication type.  May be
                     ; "userpass" for plain text passwords or
                     ; "md5" for pre-hashed credentials.
                     ; (default: "userpass")
;nonce_lifetime=32   ; Lifetime of a nonce associated with this
                     ; authentication config (default: "32")
;md5_cred=     ; As an alternative to specifying a plain text password,
               ; you can hash the username, realm and password
               ; together one time and place the hash value here.
               ; The input to the hash function must be in the
               ; following format:
               ; <username>:<realm>:<password>
               ; For incoming authentication (asterisk is the UAS),
               ; the realm must match either the realm set in this object
               ; or the default set in in the "global" object.
               ;
               ; For outgoing authentication (asterisk is the UAC),
               ; the realm must match what the server will be sending
               ; in their WWW-Authenticate header.  It can't be blank
               ; unless you expect the server to be sending a blank
               ; realm in the header.
               ; You can generate the hash with the following shell
               ; command:
               ; $ echo -n "myname:myrealm:mypassword" | md5sum
               ; Note the '-n'.  You don't want a newline to be part
               ; of the hash.  (default: "")
;password=     ; PlainText password used for authentication (default: "")
;realm=        ; For incoming authentication (asterisk is the UAS),
               ; this is the realm to be sent on WWW-Authenticate
               ; headers.  If not specified, the global object's
               ; "default_realm" will be used.
               ;
               ; For outgoing authentication (asterisk is the UAS), this
               ; must either be the realm the server is expected to send,
               ; or left blank or contain a single '*' to automatically
               ; use the realm sent by the server. If you have multiple
               ; auth object for an endpoint, the realm is also used to
               ; match the auth object to the realm the server sent.
               ; Using the same auth section for inbound and outbound
               ; authentication is not recommended.  There is a difference in
               ; meaning for an empty realm setting between inbound and outbound
               ; authentication uses.
               ; (default: "")
;type=         ; Must be auth (default: "")
;username=     ; Username to use for account (default: "")


;==========================DOMAIN_ALIAS SECTION OPTIONS=========================
;[domain_alias]
;  SYNOPSIS: Domain Alias
;type=  ; Must be of type domain_alias (default: "")
;domain=        ; Domain to be aliased (default: "")


;==========================TRANSPORT SECTION OPTIONS=========================
;[transport]
;  SYNOPSIS: SIP Transport
;
;async_operations=1     ; Number of simultaneous Asynchronous Operations
                        ; (default: "1")
;bind=  ; IP Address and optional port to bind to for this transport (default:
        ; "")
; Note that for the Websocket transport the TLS configuration is configured
; in http.conf and is applied for all HTTPS traffic.
;ca_list_file=  ; File containing a list of certificates to read TLS ONLY
                ; (default: "")
;ca_list_path=  ; Path to directory containing certificates to read TLS ONLY.
                ; PJProject version 2.4 or higher is required for this option to
                ; be used.
                ; (default: "")
;cert_file=     ; Certificate file for endpoint TLS ONLY
                ; Will read .crt or .pem file but only uses cert,
                ; a .key file must be specified via priv_key_file.
                ; Since PJProject version 2.5: If the file name ends in _rsa,
                ; for example "asterisk_rsa.pem", the files "asterisk_dsa.pem"
                ; and/or "asterisk_ecc.pem" are loaded (certificate, inter-
                ; mediates, private key), to support multiple algorithms for
                ; server authentication (RSA, DSA, ECDSA). If the chains are
                ; different, at least OpenSSL 1.0.2 is required.
                ; (default: "")
;cipher=        ; Preferred cryptography cipher names TLS ONLY (default: "")
;method=        ; Method of SSL transport TLS ONLY (default: "")
;priv_key_file= ; Private key file TLS ONLY (default: "")
;verify_client= ; Require verification of client certificate TLS ONLY (default:
                ; "")
;verify_server= ; Require verification of server certificate TLS ONLY (default:
                ; "")
;require_client_cert=   ; Require client certificate TLS ONLY (default: "")
;domain=        ; Domain the transport comes from (default: "")
;external_media_address=        ; External IP address to use in RTP handling
                                ; (default: "")
;external_signaling_address=    ; External address for SIP signalling (default:
                                ; "")
;external_signaling_port=0      ; External port for SIP signalling (default:
                                ; "0")
;local_net=     ; Network to consider local used for NAT purposes (default: "")
;password=      ; Password required for transport (default: "")
;protocol=udp   ; Protocol to use for SIP traffic (default: "udp")
;type=  ; Must be of type transport (default: "")
;tos=0  ; Enable TOS for the signalling sent over this transport (default: "0")
;cos=0  ; Enable COS for the signalling sent over this transport (default: "0")
;websocket_write_timeout=100    ; Default write timeout to set on websocket
                                ; transports. This value may need to be adjusted
                                ; for connections where Asterisk must write a
                                ; substantial amount of data and the receiving
                                ; clients are slow to process the received
                                ; information. Value is in milliseconds; default
                                ; is 100 ms.
;allow_reload=no    ; Although transports can now be reloaded, that may not be
                    ; desirable because of the slight possibility of dropped
                    ; calls. To make sure there are no unintentional drops, if
                    ; this option is set to 'no' (the default) changes to the
                    ; particular transport will be ignored. If set to 'yes',
                    ; changes (if any) will be applied.
;symmetric_transport=no ; When a request from a dynamic contact comes in on a
                        ; transport with this option set to 'yes', the transport
                        ; name will be saved and used for subsequent outgoing
                        ; requests like OPTIONS, NOTIFY and INVITE.  It's saved
                        ; as a contact uri parameter named 'x-ast-txp' and will
                        ; display with the contact uri in CLI, AMI, and ARI
                        ; output.  On the outgoing request, if a transport
                        ; wasn't explicitly set on the endpoint AND the request
                        ; URI is not a hostname, the saved transport will be
                        ; used and the 'x-ast-txp' parameter stripped from the
                        ; outgoing packet.

;==========================AOR SECTION OPTIONS=========================
;[aor]
;  SYNOPSIS: The configuration for a location of an endpoint
;contact=       ; Permanent contacts assigned to AoR (default: "")
;default_expiration=3600        ; Default expiration time in seconds for
                                ; contacts that are dynamically bound to an AoR
                                ; (default: "3600")
;mailboxes=           ; Allow subscriptions for the specified mailbox(es)
                      ; This option applies when an external entity subscribes to an AoR
                      ; for Message Waiting Indications. (default: "")
;voicemail_extension= ; The voicemail extension to send in the NOTIFY Message-Account header
                      ; (default: global/default_voicemail_extension)
;maximum_expiration=7200        ; Maximum time to keep an AoR (default: "7200")
;max_contacts=0 ; Maximum number of contacts that can bind to an AoR (default:
                ; "0")
;minimum_expiration=60  ; Minimum keep alive time for an AoR (default: "60")
;remove_existing=no     ; Allow a registration to succeed by displacing any existing
                        ; contacts that now exceed the max_contacts count.  Any
                        ; removed contacts are the next to expire.  The behaviour is
                        ; beneficial when rewrite_contact is enabled and max_contacts
                        ; is greater than one.  The removed contact is likely the old
                        ; contact created by rewrite_contact that the device is
                        ; refreshing.
                        ; (default: "no")
;remove_unavailable=no  ; If remove_existing is disabled, will allow a registration
                        ; to succeed by removing only unavailable contacts when
                        ; max_contacts is exceeded. This will reject a registration
                        ; that exceeds max_contacts if no unavailable contacts are
                        ; present to remove. If remove_existing is enabled, will
                        ; prioritize removal of unavailable contacts before removing
                        ; expiring soonest.  This tames the behavior of remove_existing
                        ; to only remove an available contact if an unavailable one is
                        ; not present.
                        ; (default: "no")
;type=  ; Must be of type aor (default: "")
;qualify_frequency=0    ; Interval at which to qualify an AoR via OPTIONS requests.
                        ; (default: "0")
;qualify_timeout=3.0      ; Qualify timeout in fractional seconds (default: "3.0")
;authenticate_qualify=no        ; Authenticates a qualify request if needed
                                ; (default: "no")
;outbound_proxy=        ; Proxy through which to send OPTIONS requests, a full SIP URI
                        ; must be provided (default: "")


;==========================SYSTEM SECTION OPTIONS=========================
;[system]
;  SYNOPSIS: Options that apply to the SIP stack as well as other system-wide settings
;timer_t1=500   ; Set transaction timer T1 value milliseconds (default: "500")
;timer_b=32000  ; Set transaction timer B value milliseconds (default: "32000")
;compact_headers=no     ; Use the short forms of common SIP header names
                        ; (default: "no")
;threadpool_initial_size=0      ; Initial number of threads in the res_pjsip
                                ; threadpool (default: "0")
;threadpool_auto_increment=5    ; The amount by which the number of threads is
                                ; incremented when necessary (default: "5")
;threadpool_idle_timeout=60     ; Number of seconds before an idle thread
                                ; should be disposed of (default: "60")
;threadpool_max_size=0  ; Maximum number of threads in the res_pjsip threadpool
                        ; A value of 0 indicates no maximum (default: "0")
;disable_tcp_switch=yes ; Disable automatic switching from UDP to TCP transports
                        ; if outgoing request is too large.
                        ; See RFC 3261 section 18.1.1.
                        ; Disabling this option has been known to cause interoperability
                        ; issues, so disable at your own risk.
                        ; (default: "yes")
;follow_early_media_fork = ; On outgoing calls, if the UAS responds with
                           ; different SDP attributes on subsequent 18X or 2XX
                           ; responses (such as a port update) AND the To tag
                           ; on the subsequent response is different than that
                           ; on the previous one, follow it.  This usually
                           ; happens when the INVITE is forked to multiple UASs
                           ; and more than 1 sends an SDP answer.
                           ; This option must also be enabled on endpoints that
                           ; require this functionality.
                           ; (default: yes)
;accept_multiple_sdp_answers =
                           ; On outgoing calls, if the UAS responds with
                           ; different SDP attributes on non-100rel 18X or 2XX
                           ; responses (such as a port update) AND the To tag on
                           ; the subsequent response is the same as that on the
                           ; previous one, process it. This can happen when the
                           ; UAS needs to change ports for some reason such as
                           ; using a separate port for custom ringback.
                           ; This option must also be enabled on endpoints that
                           ; require this functionality.
                           ; (default: no)
;disable_rport=no ; Disable the use of "rport" in outgoing requests.
;type=  ; Must be of type system (default: "")

;==========================GLOBAL SECTION OPTIONS=========================
;[global]
;  SYNOPSIS: Options that apply globally to all SIP communications
;max_forwards=70        ; Value used in Max Forwards header for SIP requests
                        ; (default: "70")
;type=  ; Must be of type global (default: "")
;user_agent=Asterisk PBX        ; Allows you to change the user agent string
                                ; The default user agent string also contains
                                ; the Asterisk version. If you don't want to
                                ; expose this, change the user_agent string.
;default_outbound_endpoint=default_outbound_endpoint    ; Endpoint to use when
                                                        ; sending an outbound
                                                        ; request to a URI
                                                        ; without a specified
                                                        ; endpoint (default: "d
                                                        ; efault_outbound_endpo
                                                        ; int")
;debug=no ; Enable/Disable SIP debug logging.  Valid options include yes|no
          ; or a host address (default: "no")
;keep_alive_interval=90 ; The interval (in seconds) at which to send (double CRLF)
                        ; keep-alives on all active connection-oriented transports;
                        ; for connection-less like UDP see qualify_frequency.
                        ; (default: "90")
;contact_expiration_check_interval=30
                        ; The interval (in seconds) to check for expired contacts.
;disable_multi_domain=no
            ; Disable Multi Domain support.
            ; If disabled it can improve realtime performace by reducing
            ; number of database requsts
            ; (default: "no")
;endpoint_identifier_order=ip,username,anonymous
            ; The order by which endpoint identifiers are given priority.
            ; Currently, "ip", "header", "username", "auth_username" and "anonymous"
            ; are valid identifiers as registered by the res_pjsip_endpoint_identifier_*
            ; modules.  Some modules like res_pjsip_endpoint_identifier_user register
            ; more than one identifier.  Use the CLI command "pjsip show identifiers"
            ; to see the identifiers currently available.
            ; (default: ip,username,anonymous)
;max_initial_qualify_time=4 ; The maximum amount of time (in seconds) from
                            ; startup that qualifies should be attempted on all
                            ; contacts.  If greater than the qualify_frequency
                            ; for an aor, qualify_frequency will be used instead.
;regcontext=sipregistrations  ; If regcontext is specified, Asterisk will dynamically
                              ; create and destroy a NoOp priority 1 extension for a
                              ; given endpoint who registers or unregisters with us.
                              ; The extension added is the name of the endpoint.
;default_voicemail_extension=asterisk
                   ; The voicemail extension to send in the NOTIFY Message-Account header
                   ; if not set on endpoint or aor.
                   ; (default: "")
;
; The following unidentified_request options are only used when "auth_username"
; matching is enabled in "endpoint_identifier_order".
;
;unidentified_request_count=5   ; The number of unidentified requests that can be
                                ; received from a single IP address in
                                ; unidentified_request_period seconds before a security
                                ; event is generated. (default: 5)
;unidentified_request_period=5  ; See above.  (default: 5 seconds)
;unidentified_request_prune_interval=30
                                ; The interval at which unidentified requests
                                ; are check to see if they can be pruned.  If they're
                                ; older than twice the unidentified_request_period,
                                ; they're pruned.
;
;default_from_user=asterisk     ; When Asterisk generates an outgoing SIP request, the
                                ; From header username will be set to this value if
                                ; there is no better option (such as CallerID or
                                ; endpoint/from_user) to be used
;default_realm=asterisk         ; When Asterisk generates a challenge, the digest realm
                                ; will be set to this value if there is no better option
                                ; (such as auth/realm) to be used.

                    ; Asterisk Task Processor Queue Size
                    ; On heavy loaded system with DB storage you may need to increase
                    ; taskprocessor queue.
                    ; If the taskprocessor queue size reached high water level,
                    ; the alert is triggered.
                    ; If the alert is set the pjsip distibutor stops processing incoming
                    ; requests until the alert is cleared.
                    ; The alert is cleared when taskprocessor queue size drops to the
                    ; low water clear level.
                    ; The next options set taskprocessor queue levels for MWI.
;mwi_tps_queue_high=500 ; Taskprocessor high water alert trigger level.
;mwi_tps_queue_low=450  ; Taskprocessor low water clear alert level.
                    ; The default is -1 for 90% of high water level.

                    ; Unsolicited MWI
                    ; If there are endpoints configured with unsolicited MWI
                    ; then res_pjsip_mwi module tries to send MWI to all endpoints on startup.
;mwi_disable_initial_unsolicited=no ; Disable sending unsolicited mwi to all endpoints on startup.
                    ; If disabled then unsolicited mwi will start processing
                    ; on the endpoint's next contact update.

;ignore_uri_user_options=no ; Enable/Disable ignoring SIP URI user field options.
                    ; If you have this option enabled and there are semicolons
                    ; in the user field of a SIP URI then the field is truncated
                    ; at the first semicolon.  This effectively makes the semicolon
                    ; a non-usable character for PJSIP endpoint names, extensions,
                    ; and AORs.  This can be useful for improving compatability with
                    ; an ITSP that likes to use user options for whatever reason.
                    ; Example:
                    ; URI: "sip:1235557890;phone-context=national@x.x.x.x;user=phone"
                    ; The user field is "1235557890;phone-context=national"
                    ; Which becomes this: "1235557890"
                    ;
                    ; Note: The caller-id and redirecting number strings obtained
                    ; from incoming SIP URI user fields are always truncated at the
                    ; first semicolon.

;send_contact_status_on_update_registration=no ; Enable sending AMI ContactStatus
                    ; event when a device refreshes its registration
                    ; (default: "no")

;taskprocessor_overload_trigger=global
                ; Set the trigger the distributor will use to detect
                ; taskprocessor overloads.  When triggered, the distributor
                ; will not accept any new requests until the overload has
                ; cleared.
                ; "global": (default) Any taskprocessor overload will trigger.
                ; "pjsip_only": Only pjsip taskprocessor overloads will trigger.
                ; "none":  No overload detection will be performed.
                ; WARNING: The "none" and "pjsip_only" options should be used
                ; with extreme caution and only to mitigate specific issues.
                ; Under certain conditions they could make things worse.

;norefersub=yes     ; Enable sending norefersub option tag in Supported header to advertise
                    ; that the User Agent is capable of accepting a REFER request with
                    ; creating an implicit subscription (see RFC 4488).
                    ; (default: "yes")

; MODULE PROVIDING BELOW SECTION(S): res_pjsip_acl
;==========================ACL SECTION OPTIONS=========================
;[acl]
;  SYNOPSIS: Access Control List
;acl=   ; List of IP ACL section names in acl conf (default: "")
;contact_acl=   ; List of Contact ACL section names in acl conf (default: "")
;contact_deny=  ; List of Contact header addresses to deny (default: "")
;contact_permit=        ; List of Contact header addresses to permit (default:
                        ; "")
;deny=  ; List of IP addresses to deny access from (default: "")
;permit=        ; List of IP addresses to permit access from (default: "")
;type=  ; Must be of type acl (default: "")




; MODULE PROVIDING BELOW SECTION(S): res_pjsip_outbound_registration
;==========================REGISTRATION SECTION OPTIONS=========================
;[registration]
;  SYNOPSIS: The configuration for outbound registration
;auth_rejection_permanent=yes   ; Determines whether failed authentication
                                ; challenges are treated as permanent failures
                                ; (default: "yes")
;client_uri=    ; Client SIP URI used when attemping outbound registration
                ; (default: "")
;contact_user=  ; Contact User to use in request (default: "")
;expiration=3600        ; Expiration time for registrations in seconds
                        ; (default: "3600")
;max_retries=10 ; Maximum number of registration attempts (default: "10")
;outbound_auth= ; Authentication object to be used for outbound registrations
                ; (default: "")
;outbound_proxy=        ; Proxy through which to send registrations, a full SIP URI
                        ; must be provided (default: "")
;retry_interval=60      ; Interval in seconds between retries if outbound
                        ; registration is unsuccessful (default: "60")
;forbidden_retry_interval=0     ; Interval used when receiving a 403 Forbidden
                                ; response (default: "0")
;fatal_retry_interval=0 ; Interval used when receiving a fatal response.
                        ; (default: "0") A fatal response is any permanent
                        ; failure (non-temporary 4xx, 5xx, 6xx) response
                        ; received from the registrar. NOTE - if also set
                        ; the 'forbidden_retry_interval' takes precedence
                        ; over this one when a 403 is received. Also, if
                        ; 'auth_rejection_permanent' equals 'yes' a 401 and
                        ; 407 become subject to this retry interval.
;server_uri=    ; SIP URI of the server to register against (default: "")
;transport=     ; Transport used for outbound authentication (default: "")
;line=          ; When enabled this option will cause a 'line' parameter to be
                ; added to the Contact header placed into the outgoing
                ; registration request. If the remote server sends a call
                ; this line parameter will be used to establish a relationship
                ; to the outbound registration, ultimately causing the
                ; configured endpoint to be used (default: "no")
;endpoint=      ; When line support is enabled this configured endpoint name
                ; is used for incoming calls that are related to the outbound
                ; registration (default: "")
;type=  ; Must be of type registration (default: "")




; MODULE PROVIDING BELOW SECTION(S): res_pjsip_endpoint_identifier_ip
;==========================IDENTIFY SECTION OPTIONS=========================
;[identify]
;  SYNOPSIS: Identifies endpoints via some criteria.
;
; NOTE: If multiple matching criteria are provided then an inbound request will
; be matched to the endpoint if it matches ANY of the criteria.
;endpoint=      ; Name of endpoint identified (default: "")
;srv_lookups=yes        ; Perform SRV lookups for provided hostnames. (default: yes)
;match= ; Comma separated list of IP addresses, networks, or hostnames to match
        ; against (default: "")
;match_header= ; SIP header with specified value to match against (default: "")
;type=  ; Must be of type identify (default: "")




;========================PHONEPROV_USER SECTION OPTIONS=======================
;[phoneprov]
;  SYNOPSIS: Contains variables for autoprovisioning each user
;endpoint=      ; The endpoint from which to gather username, secret, etc. (default: "")
;PROFILE=       ; The name of a profile configured in phoneprov.conf (default: "")
;MAC=           ; The mac address for this user (default: "")
;OTHERVAR=      ; Any other name value pair to be used in templates (default: "")
                ; Common variables include LINE, LINEKEYS, etc.
                ; See phoneprov.conf.sample for others.
;type=          ; Must be of type phoneprov (default: "")



; MODULE PROVIDING BELOW SECTION(S): res_pjsip_outbound_publish
;======================OUTBOUND_PUBLISH SECTION OPTIONS=====================
; See https://wiki.asterisk.org/wiki/display/AST/Publishing+Extension+State
; for more information.
;[outbound-publish]
;type=outbound-publish     ; Must be of type 'outbound-publish'.

;expiration=3600           ; Expiration time for publications in seconds

;outbound_auth=            ; Authentication object(s) to be used for outbound
                           ; publishes.
                           ; This is a comma-delimited list of auth sections
                           ; defined in pjsip.conf used to respond to outbound
                           ; authentication challenges.
                           ; Using the same auth section for inbound and
                           ; outbound authentication is not recommended.  There
                           ; is a difference in meaning for an empty realm
                           ; setting between inbound and outbound authentication
                           ; uses. See the auth realm description for details.

;outbound_proxy=           ; SIP URI of the outbound proxy used to send
                           ; publishes

;server_uri=               ; SIP URI of the server and entity to publish to.
                           ; This is the URI at which to find the entity and
                           ; server to send the outbound PUBLISH to.
                           ; This URI is used as the request URI of the outbound
                           ; PUBLISH request from Asterisk.

;from_uri=                 ; SIP URI to use in the From header.
                           ; This is the URI that will be placed into the From
                           ; header of outgoing PUBLISH messages. If no URI is
                           ; specified then the URI provided in server_uri will
                           ; be used.

;to_uri=                   ; SIP URI to use in the To header.
                           ; This is the URI that will be placed into the To
                           ; header of outgoing PUBLISH messages. If no URI is
                           ; specified then the URI provided in server_uri will
                           ; be used.

;event=                    ; Event type of the PUBLISH.

;max_auth_attempts=        ; Maximum number of authentication attempts before
                           ; stopping the pub.

;transport=                ; Transport used for outbound publish.
                           ; A transport configured in pjsip.conf. As with other
                           ; res_pjsip modules, this will use the first
                           ; available transport of the appropriate type if
                           ; unconfigured.

;multi_user=no             ; Enable multi-user support (Asterisk 14+ only)



; MODULE PROVIDING BELOW SECTION(S): res_pjsip_pubsub
;=============================RESOURCE-LIST===================================
; See https://wiki.asterisk.org/wiki/pages/viewpage.action?pageId=30278158
; for more information.
;[resource_list]
;type=resource_list        ; Must be of type 'resource_list'.

;event=                    ; The SIP event package that the list resource.
                           ; belongs to.  The SIP event package describes the
                           ; types of resources that Asterisk reports the state
                           ; of.

;list_item=                ; The name of a resource to report state on.
                           ; In general Asterisk looks up list items in the
                           ; following way:
                           ;  1. Check if the list item refers to another
                           ;     configured resource list.
                           ;  2. Pass the name of the resource off to
                           ;     event-package-specific handlers to find the
                           ;     specified resource.
                           ; The second part means that the way the list item
                           ; is specified depends on what type of list this is.
                           ; For instance, if you have the event set to
                           ; presence, then list items should be in the form of
                           ; dialplan_extension@dialplan_context. For
                           ; message-summary, mailbox names should be listed.

;full_state=no             ; Indicates if the entire list's state should be
                           ; sent out.
                           ; If this option is enabled, and a resource changes
                           ; state, then Asterisk will construct a notification
                           ; that contains the state of all resources in the
                           ; list. If the option is disabled, Asterisk will
                           ; construct a notification that only contains the
                           ; states of resources that have changed.
                           ; NOTE: Even with this option disabled, there are
                           ; certain situations where Asterisk is forced to send
                           ; a notification with the states of all resources in
                           ; the list. When a subscriber renews or terminates
                           ; its subscription to the list, Asterisk MUST send
                           ; a full state notification.

;notification_batch_interval=0
                           ; Time Asterisk should wait, in milliseconds,
                           ; before sending notifications.

;==========================INBOUND_PUBLICATION================================
; See https://wiki.asterisk.org/wiki/display/AST/Exchanging+Device+and+Mailbox+State+Using+PJSIP
; for more information.
;[inbound-publication]
;type=                     ; Must be of type 'inbound-publication'.

;endpoint=                 ; Optional name of an endpoint that is only allowed
                           ; to publish to this resource.


; MODULE PROVIDING BELOW SECTION(S): res_pjsip_publish_asterisk
;==========================ASTERISK_PUBLICATION===============================
; See https://wiki.asterisk.org/wiki/display/AST/Exchanging+Device+and+Mailbox+State+Using+PJSIP
; for more information.
;[asterisk-publication]
;type=asterisk-publication ; Must be of type 'asterisk-publication'.

;devicestate_publish=      ; Optional name of a publish item that can be used
                           ; to publish a req.

;mailboxstate_publish=     ; Optional name of a publish item that can be used
                           ; to publish a req.

;device_state=no           ; Whether we should permit incoming device state
                           ; events.

;device_state_filter=      ; Optional regular expression used to filter what
                           ; devices we accept events for.

;mailbox_state=no          ; Whether we should permit incoming mailbox state
                           ; events.

;mailbox_state_filter=     ; Optional regular expression used to filter what
                           ; mailboxes we accept events for.


;===============ENDPOINT CONFIGURED AS A TRUNK, OUTBOUND AUTHENTICATION=======
;
; This is one way to configure an endpoint as a trunk. It is set up with
; "outbound_auth=" to enable authentication when dialing out through this
; endpoint. There is no inbound authentication set up since a provider will
; not normally authenticate when calling you.
;
; The identify configuration enables IP address matching against this endpoint.
; For calls from a trunking provider, the From user may be different every time,
; so we want to match against IP address instead of From user.
;
; If you want the provider of your trunk to know where to send your calls
; you'll need to use an outbound registration as in the example above this
; section.
;
; NAT
;
; At a basic level configure the endpoint with a transport that is set up
; with the appropriate NAT settings. There may be some additional settings you
; need here based on your NAT/Firewall scenario. Look to the CLI config help
; "config show help res_pjsip endpoint" or on the wiki for other NAT related
; options and configuration. We've included a few below.
;
; AOR
;
; Endpoints use one or more AOR sections to store their contact details.
; You can define multiple contact addresses in SIP URI format in multiple
; "contact=" entries.
;

;===============OUTBOUND REGISTRATION WITH OUTBOUND AUTHENTICATION============
;
; This is a simple registration that works with some SIP trunking providers.
; You'll need to set up the auth example "mytrunk_auth" below to enable outbound
; authentication. Note that we use "outbound_auth=" for outbound authentication
; instead of "auth=", which is for inbound authentication.
;
; If you are registering to a server from behind NAT, be sure you assign a transport
; that is appropriately configured with NAT related settings. See the NAT transport example.
;
; "contact_user=" sets the SIP contact header's user portion of the SIP URI
; this will affect the extension reached in dialplan when the far end calls you at this
; registration. The default is 's'.
;
; If you would like to enable line support and have incoming calls related to this
; registration go to an endpoint automatically the "line" and "endpoint" options must
; be set. The "endpoint" option specifies what endpoint the incoming call should be
; associated with.

[transport-udp]
type=transport
protocol=udp    ;udp,tcp,tls,ws,wss,flow
bind=0.0.0.0


[anonymous]
type=endpoint
context=anonymous
disallow=all
allow=speex,g726,g722,ilbc,gsm,alaw

[mytrunk]
type=registration
transport=transport-udp
outbound_auth=mytrunk_auth
server_uri=sip:<?= $_ENV["SIP_HOST"] ?>
            
client_uri=sip:<?= $_ENV["SIP_USERNAME"] ?>@sipgate.de
            
contact_user=<?= $_ENV["SIP_USERNAME"] ?>
            
retry_interval=60
forbidden_retry_interval=600
expiration=3600
line=yes
endpoint=endpoint1

[mytrunk_auth]
type=auth
auth_type=userpass
password=<?= $_ENV["SIP_SECRET"] ?>
            
username=<?= $_ENV["SIP_USERNAME"] ?>
            
realm=<?= $_ENV["SIP_FROMDOMAIN"] ?>
            
            