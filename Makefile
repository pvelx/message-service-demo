generate_proto:
	protoc --proto_path=./proto \
        --php_out=./endpoint \
        --grpc_out=./endpoint \
        --plugin=protoc-gen-grpc=./bin/opt/grpc_php_plugin \
        ./proto/task.proto
