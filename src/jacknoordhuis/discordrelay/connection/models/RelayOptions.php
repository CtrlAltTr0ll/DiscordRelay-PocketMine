<?php

/**
 * RelayOptionsPM-Discord-Relay
 *
 * Copyright (C) 2018 Jack Noordhuis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Jack
 *
 */

declare(strict_types=1);

namespace jacknoordhuis\discordrelay\connection\models;

class RelayOptions implements \Serializable {

	/** @var string */
	private $token;

	/** @var RelayChannel[] */
	private $channels = [];

	public function token() : string {
		return $this->token;
	}

	public function setToken(string $token) : void {
		$this->token = $token;
	}

	/**
	 * @return RelayChannel[]
	 */
	public function channels() : array {
		return $this->channels;
	}

	/**
	 * @param string $id
	 *
	 * @return RelayChannel|null
	 */
	public function channel(string $id) : ?RelayChannel {
		return $this->channels[$id] ?? null;
	}

	public function addChannel(RelayChannel $channel) : void {
		if(isset($this->channels[$channel->id()])) {
			throw new \RuntimeException("Tried to register a relay channel twice!");
		}

		$this->channels[$channel->id()] = $channel;
	}

	public function serialize() {
		return json_encode($this->toArray(false));
	}

	public function unserialize($serialized) {
		$this->fromArray(json_decode($serialized, true));
	}

	public function __toString() {
		return json_encode($this->toArray());
	}

	public function toArray(bool $safe = true) : array {
		return [
			"token" => $safe ? str_repeat("*", 8) : $this->token,
			"channels" => array_map(function(RelayChannel $channel) {
				return $channel->toArray();
			}, $this->channels),
		];
	}

	public function fromArray(array $data) : void {
		$this->token = $data["token"];
		foreach($data["channels"] as $key => $channel) {
			$this->channels[$key] = $c = new RelayChannel();
			$c->fromArray($channel);
		}
	}

}